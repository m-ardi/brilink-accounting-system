<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AgentSetting;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * TransactionService — Inti logika bisnis kalkulasi transaksi BRILink.
 *
 * Semua kalkulasi fee, laba, dan perubahan saldo modal ditangani di sini,
 * BUKAN di Controller, sesuai prinsip Single Responsibility.
 *
 * ⚠️ PENDING VALIDATION — Formula laba bersih di sini masih provisional
 * dan belum diverifikasi ke pemilik Agen BRILink Rambo (BAB III skripsi).
 *
 * Formula provisional saat ini:
 *   Laba Bersih = fee_bank - biaya_operasional
 *   Saldo Modal Baru = Saldo Lama - nominal (untuk Transfer/Tarik Tunai)
 *                                 (untuk PPOB: saldo tidak berkurang karena
 *                                  pembayaran langsung dari nasabah)
 */
class TransactionService
{
    /**
     * Simpan transaksi baru dan perbarui saldo modal agen.
     *
     * @param  array $data  Data dari StoreTransactionRequest
     * @return Transaction  Transaksi yang baru disimpan
     * @throws RuntimeException Jika kategori tidak memiliki fee rule aktif
     */
    public function store(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = Auth::user();

            $nominal = (float) $data['nominal'];

            // 1. Ambil kategori dan fee rule aktif berdasarkan nominal
            $category = Category::findOrFail($data['category_id']);
            $feeRule  = $category->activeFeeRuleFor($nominal);

            if (! $feeRule) {
                throw new RuntimeException(
                    "Kategori '{$category->name}' tidak memiliki fee rule aktif. " .
                    "Hubungi administrator untuk mengatur fee rule terlebih dahulu."
                );
            }

            // Nominal sudah di-parsing di atas            // 2. Hitung fee bank dari nominal (⚠️ PROVISIONAL)
            $feeBank           = $feeRule->hitungFeeBank($nominal);
            $biayaOperasional  = (float) $feeRule->biaya_operasional;

            // 3. Hitung laba bersih (⚠️ PROVISIONAL — formula pending validasi lapangan)
            $labaBersih = $feeBank - $biayaOperasional;

            // 4. Hitung perubahan saldo modal
            $saldoSebelum = AgentSetting::getSaldoModal();
            $saldoSesudah = $this->hitungSaldoSesudah($saldoSebelum, $nominal, $category->name);

            // 5. Simpan transaksi (snapshot semua nilai saat ini)
            $transaction = Transaction::create([
                'user_id'             => $user->id,
                'category_id'         => $category->id,
                'nominal'             => $nominal,
                'fee_bank'            => $feeBank,
                'biaya_operasional'   => $biayaOperasional,
                'laba_bersih'         => $labaBersih,
                'saldo_modal_sebelum' => $saldoSebelum,
                'saldo_modal_sesudah' => $saldoSesudah,
                'keterangan'          => $data['keterangan'] ?? null,
            ]);

            // 6. Update saldo modal agen di agent_settings
            AgentSetting::setSaldoModal($saldoSesudah);

            // 7. Catat ke audit trail
            $this->log($user, 'created', $transaction,
                "Transaksi {$category->name} sebesar Rp " . number_format($nominal, 0, ',', '.') .
                " ditambahkan. Laba: Rp " . number_format($labaBersih, 0, ',', '.')
            );

            return $transaction;
        });
    }

    /**
     * Update transaksi yang sudah ada.
     * Saldo modal di-recalculate dari nol berdasarkan semua transaksi aktif.
     *
     * @param  Transaction $transaction  Transaksi yang akan diupdate
     * @param  array       $data         Data baru
     * @return Transaction               Transaksi setelah update
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            /** @var User $user */
            $user = Auth::user();

            $nominal = (float) $data['nominal'];
            $category = Category::findOrFail($data['category_id']);
            $feeRule  = $category->activeFeeRuleFor($nominal);

            if (! $feeRule) {
                throw new RuntimeException(
                    "Kategori '{$category->name}' tidak memiliki fee rule aktif."
                );
            }

            // Nominal sudah di-parsing di atas
            $feeBank          = $feeRule->hitungFeeBank($nominal);
            $biayaOperasional = (float) $feeRule->biaya_operasional;
            $labaBersih       = $feeBank - $biayaOperasional; // ⚠️ PROVISIONAL

            // Simpan nilai lama untuk log
            $nominalLama = $transaction->nominal;

            // Gunakan saldo sebelum transaksi ini sebagai basis
            $saldoSebelum = (float) $transaction->saldo_modal_sebelum;
            $saldoSesudah = $this->hitungSaldoSesudah($saldoSebelum, $nominal, $category->name);

            $transaction->update([
                'category_id'         => $category->id,
                'nominal'             => $nominal,
                'fee_bank'            => $feeBank,
                'biaya_operasional'   => $biayaOperasional,
                'laba_bersih'         => $labaBersih,
                'saldo_modal_sesudah' => $saldoSesudah,
                'keterangan'          => $data['keterangan'] ?? $transaction->keterangan,
            ]);

            // Recalculate saldo modal dari transaksi terakhir
            $saldoTerkini = (float) Transaction::latest()->first()?->saldo_modal_sesudah
                            ?? AgentSetting::getSaldoModal();
            AgentSetting::setSaldoModal($saldoTerkini);

            // Audit trail
            $this->log($user, 'updated', $transaction,
                "Transaksi #{$transaction->id} diubah. " .
                "Nominal: Rp " . number_format($nominalLama, 0, ',', '.') .
                " → Rp " . number_format($nominal, 0, ',', '.')
            );

            return $transaction->fresh();
        });
    }

    /**
     * Hapus transaksi dan recalculate saldo modal.
     *
     * @param  Transaction $transaction  Transaksi yang akan dihapus
     */
    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            /** @var User $user */
            $user = Auth::user();

            $transactionId = $transaction->id;
            $nominal       = $transaction->nominal;
            $categoryName  = $transaction->category->name ?? 'Unknown';

            // Catat audit SEBELUM dihapus
            $this->log($user, 'deleted', $transaction,
                "Transaksi #{$transactionId} ({$categoryName}) " .
                "Rp " . number_format($nominal, 0, ',', '.') . " dihapus."
            );

            $transaction->delete();

            // Recalculate saldo modal dari transaksi terakhir yang tersisa
            $transaksiTerakhir = Transaction::latest()->first();
            $saldoBaru = $transaksiTerakhir
                ? (float) $transaksiTerakhir->saldo_modal_sesudah
                : (float) $transaction->saldo_modal_sebelum; // Kembalikan ke saldo sebelum transaksi ini

            AgentSetting::setSaldoModal($saldoBaru);
        });
    }

    /**
     * Hitung saldo modal baru setelah transaksi.
     *
     * ⚠️ PENDING VALIDATION — Logika arah saldo (±nominal) di bawah ini
     * masih provisional. Perlu validasi ke pemilik agen:
     * - Apakah Transfer selalu mengurangi saldo modal?
     * - Bagaimana perlakuan PPOB terhadap saldo modal?
     *
     * Asumsi provisional:
     * - Transfer    : saldo berkurang sebesar nominal (kas keluar)
     * - Tarik Tunai : saldo berkurang sebesar nominal (kas keluar)
     * - PPOB        : saldo tidak berubah (pembayaran langsung dari nasabah ke loket)
     *
     * @param  float  $saldoSebelum  Saldo modal sebelum transaksi
     * @param  float  $nominal       Nominal transaksi
     * @param  string $categoryName  Nama kategori ('Transfer'|'Tarik Tunai'|'PPOB')
     * @return float                 Saldo modal sesudah transaksi
     */
    private function hitungSaldoSesudah(float $saldoSebelum, float $nominal, string $categoryName): float
    {
        // ⚠️ PROVISIONAL — pending validasi lapangan (BAB III)
        return match ($categoryName) {
            'PPOB'  => $saldoSebelum,                // PPOB: saldo tidak berubah
            default => $saldoSebelum - $nominal,     // Transfer & Tarik Tunai: kas keluar
        };
    }

    /**
     * Catat aktivitas ke tabel activity_logs.
     */
    private function log(User $user, string $action, Transaction $transaction, string $description): void
    {
        ActivityLog::create([
            'user_id'      => $user->id,
            'action'       => $action,
            'subject_type' => Transaction::class,
            'subject_id'   => $transaction->id,
            'description'  => $description,
            'created_at'   => now(),
        ]);
    }
}
