<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->text('product_for');
            $table->string('users_name');
            $table->string('c_number')->nullable();
            $table->text('comment');
            $table->integer('rating');
            $table->integer('status');
            $table->integer('is_read');
            $table->text('profile_pic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
