<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel modal agen BRILink.
 *
 * Menyimpan saldo modal berjalan agen dalam satu baris tunggal.
 * Saldo ini diperbarui setiap kali transaksi baru dibuat, diubah, atau dihapus.
 *
 * Formula update saldo per transaksi:
 *   saldo_sesudah = saldo_sebelum - nominal_transaksi + laba_bersih
 *
 * Saldo awal diinput pemilik agen di halaman Pengaturan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modal', function (Blueprint $table) {
            $table->id();

            // Saldo modal berjalan agen (diperbarui tiap transaksi)
            $table->decimal('saldo_saat_ini', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modal');
    }
};
