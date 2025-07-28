<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('user_id'); // BIGINT UNSIGNED NOT NULL
            $table->unsignedBigInteger('project_id')->nullable(); // INT UNSIGNED NULL
            $table->string('action', 255); // VARCHAR(255)
            $table->text('details')->nullable(); // TEXT
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP')); // created_at with CURRENT_TIMESTAMP

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
