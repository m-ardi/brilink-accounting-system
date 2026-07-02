<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel aturan fee per kategori transaksi BRILink.
 *
 * ⚠️ PENDING VALIDATION — Formula laba bersih di sini masih bersifat
 * provisional/hipotesis dan BELUM diverifikasi ke pemilik Agen BRILink Rambo.
 * Formula akan diperbarui setelah wawancara lapangan (BAB III skripsi).
 *
 * Formula provisional saat ini:
 *   Laba Bersih = fee_value (sharing fee bank) - biaya_operasional (biaya agen)
 *
 * Desain non-hardcoded: nilai fee & biaya operasional disimpan di tabel ini
 * sehingga dapat diubah tanpa modifikasi kode.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');

            // Tipe fee: nominal (Rp flat) atau persentase (%) dari nominal transaksi
            $table->enum('fee_type', ['nominal', 'persentase'])->default('nominal');

            // Nilai fee/sharing fee dari bank per transaksi (PROVISIONAL — pending validasi)
            $table->decimal('fee_value', 15, 2)->default(0);

            // Biaya operasional agen per transaksi (listrik, dll.) (PROVISIONAL — pending validasi)
            $table->decimal('biaya_operasional', 15, 2)->default(0);

            // Hanya satu fee_rule aktif per kategori yang dipakai saat kalkulasi
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index untuk lookup cepat saat kalkulasi transaksi
            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_rules');
    }
};
