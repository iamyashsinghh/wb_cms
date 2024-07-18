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
        Schema::table('users', function (Blueprint $table) {
            $table->time('login_start_time')->nullable();
            $table->time('login_end_time')->nullable();
            $table->tinyInteger('is_all_time_login')->default(1)->comment('0=deactive, 1=active');
            $table->tinyInteger('status')->default(1)->comment('0=deactive, 1=active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_start_time');
            $table->dropColumn('login_end_time');
            $table->dropColumn('is_all_time_login');
            $table->dropColumn('status');
        });
    }
};
