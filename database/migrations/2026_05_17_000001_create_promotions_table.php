<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('ten');                              // Tên KM: "Black Friday 2026", "1/1"
            $table->text('mo_ta')->nullable();                  // Mô tả
            $table->enum('loai_km', ['percent', 'fixed']);      // % hoặc số tiền cố định
            $table->decimal('gia_tri', 10, 2);                  // Giá trị: 45 (%) hoặc 50000 (VND)
            $table->decimal('gia_tri_toi_da', 15, 2)->nullable(); // Giảm tối đa (cho loại %)
            $table->timestamp('ngay_bat_dau');                  // Ngày bắt đầu
            $table->timestamp('ngay_ket_thuc');                 // Ngày kết thúc
            $table->enum('pham_vi', ['all', 'category', 'product'])->default('all'); // Phạm vi áp dụng
            $table->enum('trang_thai', ['active', 'inactive', 'scheduled'])->default('active');
            $table->string('tag')->nullable();                  // Tag: "HOT", "BLACK FRIDAY", etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
