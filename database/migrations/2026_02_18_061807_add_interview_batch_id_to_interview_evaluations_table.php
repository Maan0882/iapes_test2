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
        Schema::table('interview_evaluations', function (Blueprint $table) {
            $table->foreignId('interview_batch_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('interview_evaluations', function (Blueprint $table) {
            $table->dropForeign(['interview_batch_id']);
            $table->dropColumn('interview_batch_id');
        });
    }

};
