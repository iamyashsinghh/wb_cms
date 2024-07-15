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
            $table->unsignedBigInteger('venue_id')->comment('Relate with venue tables');
            $table->foreign('venue_id')->references('id')->on('venues');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->string('name');
            $table->string('venue_address');
            $table->smallInteger('min_capacity');
            $table->smallInteger('max_capacity');
            $table->smallInteger('veg_price');
            $table->smallInteger('nonveg_price');
            $table->unsignedBigInteger('budget_id');
            $table->foreign('budget_id')->references('id')->on('budgets');
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
