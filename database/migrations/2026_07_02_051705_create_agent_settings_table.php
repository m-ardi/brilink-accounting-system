<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel konfigurasi global agen — key-value store.
 *
 * Menyimpan data seperti:
 *  - saldo_modal: saldo modal berjalan agen (diupdate setiap transaksi)
 *  - nama_agen: nama lengkap agen untuk header laporan
 *  - alamat_agen: alamat untuk header laporan
 *
 * Dipisah dari tabel users agar mudah dikonfigurasi dan tidak coupled ke user record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('description', 255)->nullable(); // Keterangan setting untuk admin
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_settings');
    }
};
