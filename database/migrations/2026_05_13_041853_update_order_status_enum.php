<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Flow:
     *   pending → confirmed → shipping → completed / cancelled
     *   shipping: user có thể xác nhận "received" (→ completed) hoặc "not_received" (→ disputing)
     *   disputing: admin xử lý → completed hoặc shipping lại
     */
    public function up(): void
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

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN trang_thai ENUM(
            'pending',
            'confirmed',
            'shipping',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
