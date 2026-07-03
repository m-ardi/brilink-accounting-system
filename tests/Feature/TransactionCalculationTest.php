<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Modal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed database
        $this->seed();
    }

    public function test_transaction_formula_calculates_correctly(): void
    {
        // 1. Setup owner & modal awal
        $owner = User::where('role', 'owner')->first();
        Modal::setSaldo(10000000.00); // Rp 10.000.000

        $transferCategory = Category::where('name', 'Transfer')->first();

        // 2. Kirim transaksi Transfer sebesar Rp 200.000
        $response = $this
            ->actingAs($owner)
            ->post(route('transaksi.store'), [
                'category_id' => $transferCategory->id,
                'nominal'     => 200000.00,
                'keterangan'  => 'Test Transfer',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('transaksi.create'));

        // 3. Verifikasi perhitungan
        // Biaya Admin untuk Rp 200.000 adalah Rp 3.000, Setoran BRI adalah Rp 300
        // Laba Bersih = 3.000 - 300 = 2.700
        // Saldo Modal Sesudah = 10.000.000 - 200.000 + 2.700 = 9.802.700

        $this->assertEquals(9802700.00, Modal::getSaldo());

        $this->assertDatabaseHas('transactions', [
            'nominal'             => 200000.00,
            'biaya_admin'         => 3000.00,
            'setoran_bri'         => 300.00,
            'laba_bersih'         => 2700.00,
            'saldo_modal_sebelum' => 10000000.00,
            'saldo_modal_sesudah' => 9802700.00,
            'keterangan'          => 'Test Transfer',
        ]);
    }
}
