<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // BR-09: hanya satu tahun ajaran aktif — generated column, sintaks MySQL raw
        // karena Laravel's storedAs() belum stabil untuk kondisi IF() kompleks di semua versi.
        DB::statement("
            ALTER TABLE academic_years
            ADD COLUMN active_flag TINYINT AS (IF(is_active, 1, NULL)) STORED UNIQUE
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
