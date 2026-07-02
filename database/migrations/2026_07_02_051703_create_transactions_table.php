<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel transaksi utama Agen BRILink.
 *
 * ⚠️ PENDING VALIDATION — Kolom fee_bank, biaya_operasional, dan laba_bersih
 * menggunakan formula provisional yang belum diverifikasi ke pemilik agen.
 *
 * Snapshot value: nilai fee & biaya operasional disimpan per baris transaksi
 * (bukan FK ke fee_rules) agar laporan historis tidak berubah jika fee diupdate.
 *
 * Perubahan saldo modal: bergantung pada arah transaksi (keluar/masuk).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');

            // Nominal uang yang ditransaksikan nasabah
            $table->decimal('nominal', 15, 2);

            // Snapshot fee bank (sharing fee) saat transaksi terjadi — PROVISIONAL
            $table->decimal('fee_bank', 15, 2)->default(0);

            // Snapshot biaya operasional agen saat transaksi — PROVISIONAL
            $table->decimal('biaya_operasional', 15, 2)->default(0);

            // Laba bersih = fee_bank - biaya_operasional — PROVISIONAL
            $table->decimal('laba_bersih', 15, 2)->default(0);

            // Snapshot saldo modal sebelum dan sesudah transaksi ini
            $table->decimal('saldo_modal_sebelum', 15, 2);
            $table->decimal('saldo_modal_sesudah', 15, 2);

            // Keterangan bebas — digunakan untuk fitur pencarian riwayat
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Index untuk filter dashboard & laporan (query sering pakai date range + kategori)
            $table->index('created_at');
            $table->index('category_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
