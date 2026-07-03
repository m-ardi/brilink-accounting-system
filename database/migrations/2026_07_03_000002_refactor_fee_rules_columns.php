<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refaktor tabel fee_rules:
 *   fee_value        → biaya_admin   (fee yang dibebankan ke nasabah)
 *   biaya_operasional → setoran_bri  (bagian yang menjadi hak bank BRI)
 *
 * Formula laba: laba_bersih = biaya_admin - setoran_bri
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_rules', function (Blueprint $table) {
            // Ganti nama kolom sesuai terminologi bisnis BRILink yang telah dikonfirmasi
            $table->renameColumn('fee_value', 'biaya_admin');
            $table->renameColumn('biaya_operasional', 'setoran_bri');
        });
    }

    public function down(): void
    {
        Schema::table('fee_rules', function (Blueprint $table) {
            $table->renameColumn('biaya_admin', 'fee_value');
            $table->renameColumn('setoran_bri', 'biaya_operasional');
        });
    }
};
