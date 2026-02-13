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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('college')->nullable();
            $table->string('degree')->nullable();
            $table->string('last_exam_appeared')->nullable(); 
            $table->decimal('cgpa', 4, 2)->nullable();
            $table->string('domain')->nullable();
            $table->string('skills')->nullable();
            $table->string('resume_path')->nullable();
            $table->foreignId('interview_batch_id')
                ->nullable()
                ->constrained('interview_batches')
                ->nullOnDelete();

            $table->enum('interview_status', [
                'pending',
                'scheduled',
                'completed'
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
