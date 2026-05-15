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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don_hang')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ten_nguoi_nhan');
            $table->string('sdt_nguoi_nhan', 20);
            $table->text('dia_chi_giao_hang');
            $table->decimal('tong_tien', 15, 2);
            $table->decimal('phi_van_chuyen', 15, 2)->default(0);
            $table->decimal('giam_gia', 15, 2)->default(0);
            $table->decimal('thanh_tien', 15, 2);
            $table->enum('trang_thai', ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'])->default('pending');
            $table->enum('phuong_thuc_thanh_toan', ['cod', 'bank_transfer', 'vnpay'])->default('cod');
            $table->enum('trang_thai_thanh_toan', ['unpaid', 'paid'])->default('unpaid');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
