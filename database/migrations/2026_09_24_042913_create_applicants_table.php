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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('admission_wave_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('registration_number', 30)->nullable()->unique();
            $table->string('type', 20); // baru, pindahan
            $table->unsignedTinyInteger('target_grade');
            $table->string('status', 30)->default('draft');
            $table->string('full_name', 150);
            $table->char('gender', 1);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->char('nisn', 10)->nullable();
            $table->char('nik', 16)->nullable();
            $table->string('address', 255);
            $table->string('village', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('previous_school_name', 150)->nullable();
            $table->unsignedTinyInteger('previous_grade')->nullable();
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
            $table->index(['admission_wave_id', 'status']);
            $table->index('user_id');
            $table->index('nisn');
            $table->index(['full_name', 'birth_date']);
        });

        DB::statement("
            ALTER TABLE applicants
            ADD COLUMN nisn_active_key VARCHAR(20) AS (
                IF(
                    nisn IS NOT NULL AND academic_year_id IS NOT NULL
                    AND status IN ('dikirim','perlu_perbaikan','terverifikasi','diterima','daftar_ulang','menjadi_siswa'),
                    CONCAT(academic_year_id, '-', nisn),
                    NULL
                )
            ) STORED UNIQUE
        ");

        DB::statement("ALTER TABLE applicants ADD CONSTRAINT chk_target_grade CHECK (target_grade BETWEEN 1 AND 6)");
        DB::statement("ALTER TABLE applicants ADD CONSTRAINT chk_gender CHECK (gender IN ('L','P'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
