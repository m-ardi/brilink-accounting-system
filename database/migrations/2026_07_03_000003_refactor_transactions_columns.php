<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refaktor tabel transactions:
 *   fee_bank          → biaya_admin   (snapshot biaya admin ke nasabah saat transaksi)
 *   biaya_operasional → setoran_bri   (snapshot setoran BRI saat transaksi)
 *
 * Nilai ini adalah snapshot — tidak berubah meski fee_rules diupdate di kemudian hari,
 * sehingga laporan historis tetap akurat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('fee_bank', 'biaya_admin');
            $table->renameColumn('biaya_operasional', 'setoran_bri');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('biaya_admin', 'fee_bank');
            $table->renameColumn('setoran_bri', 'biaya_operasional');
        });
    }
};
