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
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_category_id')->constrained('vendor_categories')->onDelete('cascade');
            $table->string('brand_name');
            $table->string('vendor_address');
            $table->string('package_price')->nullable();
            $table->char('yrs_exp')->nullable();
            $table->char('event_completed')->nullable();
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
