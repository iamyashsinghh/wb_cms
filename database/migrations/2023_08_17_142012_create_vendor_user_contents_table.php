<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('vendor_user_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->comment("Relate with vendors tables");
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('vendor_category_id');
            $table->foreign('vendor_category_id')->references('id')->on('vendor_categories');
            $table->string('brand_name');
            $table->string('vendor_address');
            $table->string('package_price')->nullable();
            $table->char('yrs_exp')->default(0);
            $table->char('event_completed')->default(0);
            $table->mediumText('images')->nullable();
            $table->mediumText('package_option')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('vendor_user_contents');
    }
};
