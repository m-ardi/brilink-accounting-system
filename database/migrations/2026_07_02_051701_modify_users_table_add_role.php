<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom `role` ke tabel users yang sudah dibuat oleh Breeze.
 * Default: 'owner' — satu-satunya role yang aktif saat ini.
 * Kolom disiapkan untuk scale-up ke multi-user (owner + staff) tanpa
 * perlu mengubah struktur database dari awal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'staff'])->default('owner')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
