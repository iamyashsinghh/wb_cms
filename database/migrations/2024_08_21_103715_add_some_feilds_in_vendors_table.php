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
        Schema::table('vendors', function (Blueprint $table) {
            $table->text('air_brush_makeup_price')->nullable();
            $table->text('hd_bridal_makeup_price')->nullable();
            $table->text('engagement_makeup_price')->nullable();
            $table->text('party_makeup_price')->nullable();
            $table->text('cinematography_price')->nullable();
            $table->text('candid_photography_price')->nullable();
            $table->text('traditional_photography_price')->nullable();
            $table->text('traditional_video_price')->nullable();
            $table->text('pre_wedding_photoshoot_price')->nullable();
            $table->text('albums_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('air_brush_makeup_price');
            $table->dropColumn('hd_bridal_makeup_price');
            $table->dropColumn('engagement_makeup_price');
            $table->dropColumn('party_makeup_price');
            $table->dropColumn('cinematography_price');
            $table->dropColumn('candid_photography_price');
            $table->dropColumn('traditional_photography_price');
            $table->dropColumn('traditional_video_price');
            $table->dropColumn('pre_wedding_photoshoot_price');
            $table->dropColumn('albums_price');
        });
    }
};
