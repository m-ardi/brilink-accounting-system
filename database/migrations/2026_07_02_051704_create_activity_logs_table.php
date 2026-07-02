<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel audit trail — mencatat setiap aksi create/update/delete pada data transaksi.
 * Fitur keamanan utama: memastikan setiap perubahan bisa ditelusuri balik ke user & waktu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // Jenis aksi: created | updated | deleted
            $table->string('action', 50);

            // Polymorphic reference ke model yang diubah (misal: App\Models\Transaction)
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Deskripsi perubahan yang bisa dibaca manusia
            $table->text('description');

            // Hanya created_at — log tidak pernah diupdate
            $table->timestamp('created_at')->useCurrent();

            // Index untuk filter log per user dan per waktu
            $table->index('user_id');
            $table->index('created_at');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
