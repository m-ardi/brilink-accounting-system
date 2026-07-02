<?php

namespace Database\Seeders;

use App\Models\AgentSetting;
use App\Models\Category;
use App\Models\FeeRule;
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
     * 3. Fee rules provisional per kategori (⚠️ PENDING VALIDATION)
     * 4. Saldo modal awal dan konfigurasi agen
     */
    public function run(): void
    {
        // ─── 1. USER OWNER ────────────────────────────────────────────────
        $owner = User::firstOrCreate(
            ['email' => 'owner@brilink-rambo.id'],
            [
                'name'     => 'Pemilik Agen BRILink Rambo',
                'password' => Hash::make('password123'),
                'role'     => 'owner',
            ]
        );

        // ─── 2. KATEGORI LAYANAN ──────────────────────────────────────────
        $categories = [
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

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // ─── 3. FEE RULES BERTINGKAT (TIERED) ────────────────────────────
        // Formula: Laba Bersih = fee_value - biaya_operasional

        $feeRulesData = [
            'Transfer' => [
                ['min' => 1, 'max' => 1000000, 'fee' => 5000, 'ops' => 500],
                ['min' => 1000001, 'max' => 5000000, 'fee' => 10000, 'ops' => 500],
                ['min' => 5000001, 'max' => 10000000, 'fee' => 15000, 'ops' => 500],
                ['min' => 10000001, 'max' => null, 'fee' => 20000, 'ops' => 500],
            ],
            'Tarik Tunai' => [
                ['min' => 1, 'max' => 1000000, 'fee' => 5000, 'ops' => 500],
                ['min' => 1000001, 'max' => 5000000, 'fee' => 10000, 'ops' => 500],
                ['min' => 5000001, 'max' => 10000000, 'fee' => 15000, 'ops' => 500],
                ['min' => 10000001, 'max' => null, 'fee' => 20000, 'ops' => 500],
            ],
            'PPOB' => [
                ['min' => 1, 'max' => null, 'fee' => 3000, 'ops' => 500], // Flat
            ],
        ];

        foreach ($feeRulesData as $categoryName => $tiers) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                foreach ($tiers as $tier) {
                    FeeRule::create([
                        'category_id'       => $category->id,
                        'min_nominal'       => $tier['min'],
                        'max_nominal'       => $tier['max'],
                        'fee_type'          => 'nominal',
                        'fee_value'         => $tier['fee'],
                        'biaya_operasional' => $tier['ops'],
                        'is_active'         => true,
                    ]);
                }
            }
        }

        // ─── 4. KONFIGURASI AGEN ─────────────────────────────────────────
        $settings = [
            [
                'key'         => 'saldo_modal',
                'value'       => '5000000', // Saldo modal awal: Rp 5.000.000 (sesuaikan)
                'description' => 'Saldo modal agen saat ini — diupdate otomatis setiap transaksi',
            ],
            [
                'key'         => 'nama_agen',
                'value'       => 'Agen BRILink Rambo',
                'description' => 'Nama agen untuk header laporan',
            ],
            [
                'key'         => 'alamat_agen',
                'value'       => 'Dr. Ratulangi Kilometer 5',
                'description' => 'Alamat agen untuk header laporan',
            ],
            [
                'key'         => 'kode_agen',
                'value'       => 'RAMBO-001',
                'description' => 'Kode unik agen BRILink',
            ],
        ];

        foreach ($settings as $setting) {
            AgentSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'description' => $setting['description']]
            );
        }

        $this->command->info('✅ Seeder selesai: 1 user owner, 3 kategori, fee rules provisional, konfigurasi agen');
        $this->command->warn('⚠️  Fee rules masih PROVISIONAL — verifikasi ke pemilik agen sebelum go-live!');
    }
}
