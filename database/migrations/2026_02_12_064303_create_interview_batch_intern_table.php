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
        Schema::create('interview_batch_intern', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_batch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('application_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_present')->default(false);
            $table->timestamps();

            $table->unique(['interview_batch_id', 'application_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_batch_intern');
    }
};
