<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->mediumText('meta_title')->nullable();
            $table->mediumText('meta_description')->nullable();
            $table->mediumText('meta_keywords')->nullable();
            $table->mediumText('heading')->nullable();
            $table->mediumText('excerpt')->nullable();
            $table->mediumText('image')->nullable();
            $table->mediumText('image_alt')->nullable();
            $table->text('summary')->nullable();
            $table->mediumText('og_title')->nullable();
            $table->mediumText('og_description')->nullable();
            $table->mediumText('header_text')->nullable();
            $table->mediumText('footer_text')->nullable();
            $table->mediumText('category')->nullable();
            $table->mediumText('tag')->nullable();
            $table->smallInteger('status')->default(1);
            $table->smallInteger('author_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
