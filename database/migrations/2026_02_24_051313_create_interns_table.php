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
        Schema::create('interns', function (Blueprint $table) {
            $table->id();
            $table->string('intern_id')->unique(); // For TS(Year)/WD/(no)
            $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('college')->nullable();
            $table->string('degree')->nullable();
            $table->string('last_exam_appeared')->nullable(); 
            $table->decimal('cgpa', 4, 2)->nullable();
            $table->string('domain')->nullable();
            $table->string('skills')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interns');
    }
};
