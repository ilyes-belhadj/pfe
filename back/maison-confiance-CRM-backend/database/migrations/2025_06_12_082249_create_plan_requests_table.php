<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_requests', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('project_id');
            $table->dateTime('request_date')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->string('status', 50)->nullable(); // VARCHAR(50)
            $table->text('notes')->nullable(); // TEXT
            $table->timestamps(); // created_at, updated_at

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_requests');
    }
};
