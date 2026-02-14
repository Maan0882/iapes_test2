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
        Schema::table('interview_batches', function (Blueprint $table) {
            $table->enum('capacity_status', ['open','full'])->default('open');
            $table->enum('workflow_status', ['scheduled','completed','canceled'])->default('scheduled');
        }); 

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
