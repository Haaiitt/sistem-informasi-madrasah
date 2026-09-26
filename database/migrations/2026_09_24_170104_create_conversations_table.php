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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('applicant_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('subject', 150);
            $table->string('started_by_side', 10); // peserta, admin
            $table->dateTime('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'last_message_at']);
            $table->index('applicant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
