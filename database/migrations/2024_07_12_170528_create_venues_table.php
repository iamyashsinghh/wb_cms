<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('venue_address');
            $table->smallInteger('min_capacity');
            $table->smallInteger('max_capacity');
            $table->smallInteger('veg_price')->nullable();
            $table->mediumText('veg_foods')->nullable();
            $table->smallInteger('nonveg_price')->nullable();
            $table->mediumText('nonveg_foods')->nullable();
            $table->string('venue_category_ids');
            $table->string('related_location_ids')->nullable();
            $table->string('similar_venue_ids')->nullable();
            $table->time('start_time_morning')->nullable();
            $table->time('end_time_morning')->nullable();
            $table->time('start_time_evening')->nullable();
            $table->time('end_time_evening')->nullable();
            $table->mediumText('area_capacity')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->mediumText('summary')->nullable();
            $table->mediumText('images')->nullable();
            $table->mediumText('advance')->nullable();
            $table->mediumText('cancellation_policy')->nullable();
            $table->mediumText('parking_at')->nullable();
            $table->mediumText('tax_charges')->nullable();
            $table->mediumText('alcohol')->nullable();
            $table->mediumText('food')->nullable();
            $table->mediumText('decoration')->nullable();
            $table->mediumText('faq')->nullable();
            $table->mediumText('location_map')->nullable();
            $table->boolean('wb_assured')->default(false)->comment('0=false, 1=true');
            $table->boolean('popular')->default(false)->comment('0=false, 1=true');
            $table->boolean('status')->default(false)->comment('0=false, 1=true');
            $table->string('review_id')->nullable();
            $table->string('location_place_id')->nullable();
            $table->string('place_rating')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('venues');
    }
};
