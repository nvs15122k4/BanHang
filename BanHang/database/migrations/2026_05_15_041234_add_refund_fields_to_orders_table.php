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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('previous_trang_thai')->nullable();
            $table->string('refund_bank_name')->nullable();
            $table->string('refund_account_number')->nullable();
            $table->string('refund_account_name')->nullable();
            $table->text('refund_user_note')->nullable();
            $table->string('refund_status')->default('none'); // none, pending, completed
            $table->text('refund_admin_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'previous_trang_thai',
                'refund_bank_name',
                'refund_account_number',
                'refund_account_name',
                'refund_user_note',
                'refund_status',
                'refund_admin_note'
            ]);
        });
    }
};
