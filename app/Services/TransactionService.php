<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Modal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * TransactionService — Inti logika bisnis transaksi BRILink.
 *
 * Formula kalkulasi yang berlaku (dikonfirmasi pemilik agen):
 *
 *   biaya_admin   = fee yang dibebankan agen kepada nasabah (dari fee_rules)
 *   setoran_bri   = bagian yang menjadi hak bank BRI (dari fee_rules)
 *   laba_bersih   = biaya_admin - setoran_bri
 *
 *   saldo_sesudah = saldo_sebelum - nominal_transaksi + laba_bersih
 *
 * Alasan rumus saldo:
 *   Agen mengeluarkan uang sejumlah nominal untuk melayani nasabah (saldo berkurang),
 *   lalu menerima kembali fee/laba dari transaksi tersebut (saldo bertambah).
 *   Rumus ini berlaku sama untuk semua kategori: Transfer, Tarik Tunai, dan PPOB.
 *
 * Sumber saldo: tabel `modal` (bukan agent_settings).
 * Logika fee   : tabel `fee_rules` (tidak hardcoded di kode).
 * Lokasi logika: Service class ini, bukan Controller.
 */
class TransactionService
{
    /**
     * Simpan transaksi baru dan perbarui saldo modal agen.
     *
     * @param  array 
     * @return Transaction  
     * @throws RuntimeException 
     */
    public function store(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            /** @var User $user */
            $user    = Auth::user();
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

            // 2. Kalkulasi finansial dari fee_rules
            $biayaAdmin  = $feeRule->getBiayaAdmin($nominal); // fee ke nasabah
            $setoranBri  = (float) $feeRule->setoran_bri;      // hak bank
            $labaBersih  = $biayaAdmin - $setoranBri;

            // 3. Hitung perubahan saldo modal
            $saldoSebelum = Modal::getSaldo();
            $saldoSesudah = $saldoSebelum - $nominal + $labaBersih;

            // 4. Simpan transaksi (snapshot semua nilai finansial saat ini)
            $transaction = Transaction::create([
                'user_id'             => $user->id,
                'category_id'         => $category->id,
                'nominal'             => $nominal,
                'biaya_admin'         => $biayaAdmin,
                'setoran_bri'         => $setoranBri,
                'laba_bersih'         => $labaBersih,
                'saldo_modal_sebelum' => $saldoSebelum,
                'saldo_modal_sesudah' => $saldoSesudah,
                'keterangan'          => $data['keterangan'] ?? null,
            ]);

            // 5. Update saldo modal di tabel modal
            Modal::setSaldo($saldoSesudah);

            // 6. Catat ke audit trail
            $this->log(
                $user, 'created', $transaction,
                "Transaksi {$category->name} Rp " . number_format($nominal, 0, ',', '.') .
                " — Laba: Rp " . number_format($labaBersih, 0, ',', '.') .
                " — Saldo: Rp " . number_format($saldoSesudah, 0, ',', '.')
            );

            return $transaction;
        });
    }

    /**
     * Update transaksi yang sudah ada dan recalculate saldo modal.
     *
     * @param  Transaction $
     * @param  array     
     * @return Transaction               
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            /** @var User $user */
            $user    = Auth::user();
            $nominal = (float) $data['nominal'];

            $category = Category::findOrFail($data['category_id']);
            $feeRule  = $category->activeFeeRuleFor($nominal);

            if (! $feeRule) {
                throw new RuntimeException(
                    "Kategori '{$category->name}' tidak memiliki fee rule aktif."
                );
            }

            $biayaAdmin = $feeRule->getBiayaAdmin($nominal);
            $setoranBri = (float) $feeRule->setoran_bri;
            $labaBersih = $biayaAdmin - $setoranBri;

            // Gunakan saldo sebelum transaksi ini sebagai titik awal recalculation
            $saldoSebelum = (float) $transaction->saldo_modal_sebelum;
            $saldoSesudah = $saldoSebelum - $nominal + $labaBersih;

            $nominalLama = $transaction->nominal;

            $transaction->update([
                'category_id'         => $category->id,
                'nominal'             => $nominal,
                'biaya_admin'         => $biayaAdmin,
                'setoran_bri'         => $setoranBri,
                'laba_bersih'         => $labaBersih,
                'saldo_modal_sesudah' => $saldoSesudah,
                'keterangan'          => $data['keterangan'] ?? $transaction->keterangan,
            ]);

            // Sync saldo modal ke nilai akhir dari transaksi terbaru
            $saldoTerkini = (float) (Transaction::latest()->first()?->saldo_modal_sesudah
                            ?? Modal::getSaldo());
            Modal::setSaldo($saldoTerkini);

            $this->log(
                $user, 'updated', $transaction,
                "Transaksi #{$transaction->id} diubah. " .
                "Nominal: Rp " . number_format($nominalLama, 0, ',', '.') .
                " → Rp " . number_format($nominal, 0, ',', '.')
            );

            return $transaction->fresh();
        });
    }

    /**
     * Hapus transaksi dan recalculate saldo modal ke transaksi terakhir yang tersisa.
     *
     * @param  Transaction $transaction  Transaksi yang akan dihapus
     */
    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            /** @var User $user */
            $user          = Auth::user();
            $transactionId = $transaction->id;
            $nominal       = $transaction->nominal;
            $categoryName  = $transaction->category->name ?? 'Unknown';

            // Catat audit SEBELUM dihapus agar ID masih valid
            $this->log(
                $user, 'deleted', $transaction,
                "Transaksi #{$transactionId} ({$categoryName}) " .
                "Rp " . number_format($nominal, 0, ',', '.') . " dihapus."
            );

            $transaction->delete();

            // Kembalikan saldo ke transaksi terakhir yang tersisa,
            // atau ke saldo sebelum transaksi yang dihapus jika sudah tidak ada transaksi
            $transaksiTerakhir = Transaction::latest()->first();
            $saldoBaru = $transaksiTerakhir
                ? (float) $transaksiTerakhir->saldo_modal_sesudah
                : (float) $transaction->saldo_modal_sebelum;

            Modal::setSaldo($saldoBaru);
        });
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
