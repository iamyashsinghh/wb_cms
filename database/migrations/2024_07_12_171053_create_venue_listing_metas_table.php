<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('venue_listing_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('venue_categories')->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('slug');
            $table->mediumText('meta_title')->nullable();
            $table->mediumText('meta_description')->nullable();
            $table->mediumText('meta_keywords')->nullable();
            $table->longText('caption')->nullable();
            $table->mediumText('faq')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=false, 1=true');
            $table->string('page_heading')->nullable();
            $table->string('page_venues')->nullable();
            $table->string('page_vendors')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('venue_listing_metas');
    }
};
