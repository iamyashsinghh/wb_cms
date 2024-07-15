<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('venue_user_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('venue_address');
            $table->smallInteger('min_capacity');
            $table->smallInteger('max_capacity');
            $table->smallInteger('veg_price')->nullable();
            $table->smallInteger('nonveg_price')->nullable();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('venue_category_ids');
            $table->time('start_time_morning')->nullable();
            $table->time('end_time_morning')->nullable();
            $table->time('start_time_evening')->nullable();
            $table->time('end_time_evening')->nullable();
            $table->mediumText('area_capacity')->nullable();
            $table->mediumText('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('venue_user_contents');
    }
};
