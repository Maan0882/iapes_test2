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
        Schema::create('interview_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name');
            $table->date('interview_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('batch_size');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_batches');
    }
};
