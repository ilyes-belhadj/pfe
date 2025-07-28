<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_progress', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('project_id'); // INT UNSIGNED NOT NULL
            $table->dateTime('progress_date')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->text('progress_description')->nullable(); // TEXT
            $table->integer('progress_percent'); // INT
            $table->timestamps(); // created_at, updated_at

            // Foreign key
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_progress');
    }
};
