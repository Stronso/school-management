<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedToCourseSubscriptions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_subscriptions', function (Blueprint $table) {
            $table->boolean('approved')->default(false)->after('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_subscriptions', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
}
