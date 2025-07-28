<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('client');
            $table->string('email');
            $table->string('phone');
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('budget', 15, 2);
            $table->string('status')->default('draft');
            $table->integer('progress')->default(0);
            $table->boolean('redirect_to_plan_request')->default(false);

            // Nullable fields
            $table->string('agency')->nullable();
            $table->string('commercial_name')->nullable();
            $table->string('facade_color')->nullable();
            $table->string('garage_type')->nullable();
            $table->string('garage_dimensions')->nullable();
            $table->string('heating_type')->nullable();
            $table->string('house_dimensions')->nullable();
            $table->string('house_type')->nullable();
            $table->string('kitchen_type')->nullable();
            $table->decimal('livable_area', 10, 2)->nullable();
            $table->decimal('living_room_size', 10, 2)->nullable();
            $table->integer('bedrooms_count')->nullable();
            $table->text('bedrooms_size')->nullable();
            $table->integer('bathrooms_count')->nullable();
            $table->string('basement_type')->nullable();
            $table->decimal('roof_angle', 5, 2)->nullable();
            $table->string('tile_color')->nullable();
            $table->string('window_type')->nullable();
            $table->json('additional_options')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('deadline')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
