<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_category_id')->constrained()->onDelete('cascade');
            $table->string('brand_name');
            $table->string('slug');
            $table->string('phone');
            $table->string('vendor_address');
            $table->string('package_price')->nullable();
            $table->char('yrs_exp')->nullable();
            $table->char('event_completed')->nullable();
            $table->string('related_location_ids')->nullable();
            $table->mediumText('summary')->nullable();
            $table->mediumText('images')->nullable();
            $table->string('similar_vendor_ids')->nullable()->comment('Contains array');
            $table->mediumText('package_option')->nullable();
            $table->mediumText('meta_title')->nullable();
            $table->mediumText('meta_description')->nullable();
            $table->mediumText('meta_keywords')->nullable();
            $table->boolean('wb_assured')->default(false)->comment('0=false, 1=true');
            $table->boolean('popular')->default(false)->comment('0=false, 1=true');
            $table->boolean('status')->default(false)->comment('0=false, 1=true');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('vendors');
    }
};
