<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_waves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->dateTime('opens_at');
            $table->dateTime('closes_at')->nullable();
            $table->string('status', 20)->default('dijadwalkan'); // dijadwalkan, dibuka, ditutup
            $table->dateTime('results_announced_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'opens_at']);
            $table->index(['status', 'closes_at']);
        });

        // BR-14: hanya satu gelombang berstatus 'dibuka' — generated column UNIQUE.
        DB::statement("
            ALTER TABLE admission_waves
            ADD COLUMN open_flag TINYINT AS (IF(status = 'dibuka', 1, NULL)) STORED UNIQUE
        ");

        DB::statement("
            ALTER TABLE admission_waves
            ADD CONSTRAINT chk_wave_schedule CHECK (closes_at IS NULL OR closes_at > opens_at)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_waves');
    }
};
