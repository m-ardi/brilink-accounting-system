<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fee_rules', function (Blueprint $table) {
            $table->decimal('min_nominal', 15, 2)->default(0)->after('category_id');
            $table->decimal('max_nominal', 15, 2)->nullable()->after('min_nominal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_rules', function (Blueprint $table) {
            $table->dropColumn(['min_nominal', 'max_nominal']);
        });
    }
};
