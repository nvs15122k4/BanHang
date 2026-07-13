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
        Schema::table('promotions', function (Blueprint $table) {
            $table->integer('used_count')->default(0)->after('tag');
            $table->integer('usage_limit')->nullable()->after('used_count');
            $table->integer('usage_limit_per_user')->nullable()->after('usage_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['used_count', 'usage_limit', 'usage_limit_per_user']);
        });
    }
};
