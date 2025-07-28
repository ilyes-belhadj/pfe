<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('quote_request_id')->nullable(); // INT UNSIGNED NULL
            $table->unsignedBigInteger('project_id'); // INT UNSIGNED NOT NULL
            $table->decimal('amount', 15, 2); // DECIMAL(15,2)
            $table->dateTime('send_date')->nullable(); // DATETIME
            $table->boolean('accepted')->default(false); // BOOLEAN DEFAULT FALSE
            $table->timestamps(); // created_at, updated_at

            // Foreign keys
            $table->foreign('quote_request_id')->references('id')->on('quote_requests')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
