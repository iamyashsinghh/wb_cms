<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('business_users', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('business_type')->comment("Hardcore: 1=Venue, 2=Vendor");
            $table->smallInteger('business_category_id')->comment("Relate with venue_categories or vendor_categories tables.");
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('name');
            $table->string('business_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->text('about')->nullable();
            $table->boolean('user_status')->default(false)->comment("0=updated, 1=pending, 2=reject");
            $table->boolean('content_status')->default(false)->comment("0=updated, 1=pending, 2=reject");
            $table->boolean('images_status')->default(false)->comment("0=updated, 1=pending, 2=reject");
            $table->unsignedBigInteger('migrated_business_id')->nullable()->comment('Relate with venues or vendors tables.');
            $table->mediumInteger('otp_code')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void {
        Schema::dropIfExists('business_users');
    }
};
