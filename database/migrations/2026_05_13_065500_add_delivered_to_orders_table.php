<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm 'delivered' vào ENUM
        DB::statement("ALTER TABLE orders MODIFY COLUMN trang_thai ENUM(
            'pending',
            'confirmed',
            'shipping',
            'delivered',
            'disputing',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN trang_thai ENUM(
            'pending',
            'confirmed',
            'shipping',
            'disputing',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
