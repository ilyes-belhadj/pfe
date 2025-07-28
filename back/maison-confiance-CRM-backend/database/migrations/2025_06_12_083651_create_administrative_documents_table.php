<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_documents', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('project_id'); // INT UNSIGNED NOT NULL
            $table->string('document_type', 100); // VARCHAR(100)
            $table->string('document_path', 255); // VARCHAR(255)
            $table->dateTime('uploaded_at')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->timestamps(); // created_at, updated_at

            // Foreign key
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_documents');
    }
};
