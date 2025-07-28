<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('user_id')->nullable(); // BIGINT UNSIGNED NULL
            $table->unsignedBigInteger('project_id')->nullable(); // INT UNSIGNED NULL
            $table->text('content'); // TEXT
            $table->boolean('is_read')->default(false); // BOOLEAN DEFAULT FALSE
            $table->timestamps(); // created_at, updated_at

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
