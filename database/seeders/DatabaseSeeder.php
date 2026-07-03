<?php

namespace Database\Seeders;

use App\Models\AgentSetting;
use App\Models\Category;
use App\Models\FeeRule;
use App\Models\Modal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data awal aplikasi BRILink Rambo.
     *
     * Data yang di-seed:
     * 1. User owner (pemilik agen)
     * 2. 3 Kategori layanan: Transfer, Tarik Tunai, PPOB
     * 3. Fee rules per kategori (biaya_admin & setoran_bri)
     * 4. Saldo modal awal di tabel modal (Rp 0 — diisi pemilik lewat Pengaturan)
     * 5. Identitas agen di agent_settings
     */
    public function run(): void
    {
        // ─── 1. USER OWNER ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'owner@brilink-rambo.id'],
            [
                'name'     => 'Pemilik Agen BRILink Rambo',
                'password' => Hash::make('password123'),
                'role'     => 'owner',
            ]
        );

        // ─── 2. KATEGORI LAYANAN ──────────────────────────────────────────
        $kategoris = [
            [
                'name'        => 'Transfer',
                'description' => 'Layanan transfer uang antar rekening via BRILink',
            ],
            [
                'name'        => 'Tarik Tunai',
                'description' => 'Layanan penarikan tunai rekening BRI via BRILink',
            ],
            [
                'name'        => 'PPOB',
                'description' => 'Pembayaran tagihan (listrik, air, internet, dll.) via BRILink',
            ],
        ];

        foreach ($kategoris as $kat) {
            Category::firstOrCreate(['name' => $kat['name']], $kat);
        }

        // ─── 3. FEE RULES BERTINGKAT ─────────────────────────────────────
        // biaya_admin = fee yang dibebankan agen kepada nasabah (pendapatan agen)
        // setoran_bri = bagian yang menjadi hak bank BRI (biaya agen)
        // laba_bersih = biaya_admin - setoran_bri  (dihitung di TransactionService)
        //
        // Referensi riset lapangan (BRILink Sulawesi Selatan/Palopo, 2024):
        //   Transfer:    nasabah biasanya dikenakan Rp 3.000 – Rp 15.000
        //   Tarik Tunai: fee sedikit lebih tinggi (agen keluarkan uang fisik)
        //   PPOB:        fee flat Rp 2.000 – Rp 3.000 per tagihan
        //   setoran_bri: estimasi ~10% dari biaya_admin (sharing fee BRI)

        $feeRulesData = [
            'Transfer' => [
                // Nominal ≤ 500 ribu: transaksi kecil, fee minimal
                ['min' => 1,        'max' => 500000,   'admin' => 3000,  'bri' => 300],
                // 500 ribu – 1 juta
                ['min' => 500001,   'max' => 1000000,  'admin' => 5000,  'bri' => 500],
                // 1 juta – 5 juta
                ['min' => 1000001,  'max' => 5000000,  'admin' => 7000,  'bri' => 700],
                // 5 juta – 10 juta
                ['min' => 5000001,  'max' => 10000000, 'admin' => 10000, 'bri' => 1000],
                // > 10 juta
                ['min' => 10000001, 'max' => null,     'admin' => 15000, 'bri' => 1500],
            ],
            'Tarik Tunai' => [
                // Agen keluar uang fisik → risiko lebih tinggi → fee lebih besar
                ['min' => 1,        'max' => 500000,   'admin' => 5000,  'bri' => 500],
                ['min' => 500001,   'max' => 1000000,  'admin' => 7000,  'bri' => 700],
                ['min' => 1000001,  'max' => 5000000,  'admin' => 10000, 'bri' => 1000],
                ['min' => 5000001,  'max' => 10000000, 'admin' => 12000, 'bri' => 1200],
                ['min' => 10000001, 'max' => null,     'admin' => 15000, 'bri' => 1500],
            ],
            'PPOB' => [
                // Flat untuk semua nominal tagihan (PLN, air, internet, dll.)
                ['min' => 1, 'max' => null, 'admin' => 2500, 'bri' => 250],
            ],
        ];

        foreach ($feeRulesData as $categoryName => $tiers) {
            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }

            foreach ($tiers as $tier) {
                FeeRule::create([
                    'category_id'  => $category->id,
                    'min_nominal'  => $tier['min'],
                    'max_nominal'  => $tier['max'],
                    'fee_type'     => 'nominal',
                    'biaya_admin'  => $tier['admin'],   // fee ke nasabah
                    'setoran_bri'  => $tier['bri'],     // hak bank
                    'is_active'    => true,
                ]);
            }
        }

        // ─── 4. SALDO MODAL AWAL (tabel modal) ───────────────────────────
        // Dimulai dari Rp 0 — pemilik agen mengisi sendiri lewat halaman Pengaturan
        Modal::updateOrCreate([], ['saldo_saat_ini' => 0]);

        // ─── 5. IDENTITAS AGEN (agent_settings) ──────────────────────────
        $identitas = [
            [
                'key'         => 'nama_agen',
                'value'       => 'Agen BRILink Rambo',
                'description' => 'Nama agen untuk header laporan',
            ],
            [
                'key'         => 'alamat_agen',
                'value'       => 'Jl. Dr. Ratulangi Kilometer 5, Palopo',
                'description' => 'Alamat agen untuk header laporan',
            ],
            [
                'key'         => 'kode_agen',
                'value'       => 'RAMBO-001',
                'description' => 'Kode unik agen BRILink',
            ],
        ];

        foreach ($identitas as $item) {
            AgentSetting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'], 'description' => $item['description']]
            );
        }

        $this->command->info('✅ Seeder selesai: 1 user owner, 3 kategori, fee rules final, saldo modal Rp 0');
        $this->command->line('   ℹ️  Isi saldo modal awal via halaman Pengaturan Agen sebelum mulai input transaksi.');
    }
}
