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
        Schema::create('applicant_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('relation', 10); // ayah, ibu, wali
            $table->string('full_name', 150);
            $table->string('occupation', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();
            $table->unique(['applicant_id', 'relation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_guardians');
    }
};
