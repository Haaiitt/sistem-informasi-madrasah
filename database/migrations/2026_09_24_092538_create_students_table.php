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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->char('nisn', 10)->nullable()->unique();
            $table->char('nik', 16)->nullable();
            $table->string('full_name', 150);
            $table->char('gender', 1);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->string('address', 255);
            $table->string('village', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('status', 20)->default('aktif'); // aktif, lulus, pindah, keluar
            $table->date('entered_on');
            $table->date('left_on')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('full_name');
        });

        DB::statement("ALTER TABLE students ADD CONSTRAINT chk_student_gender CHECK (gender IN ('L','P'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
