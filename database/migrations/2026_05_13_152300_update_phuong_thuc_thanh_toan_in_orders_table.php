<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For changing enum columns, Doctrine DBAL might have issues.
        // The safest cross-database way is to use raw SQL for enum or change to string.
        // Let's modify the enum by raw query in MySQL
        DB::statement("ALTER TABLE orders MODIFY COLUMN phuong_thuc_thanh_toan ENUM('cod', 'bank_transfer', 'vnpay', 'vietqr') DEFAULT 'vietqr'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN phuong_thuc_thanh_toan ENUM('cod', 'bank_transfer', 'vnpay') DEFAULT 'cod'");
    }
};
