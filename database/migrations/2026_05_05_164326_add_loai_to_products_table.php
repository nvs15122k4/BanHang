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
        Schema::table('products', function (Blueprint $table) {
            $table->string('loai')->nullable()->after('ten_sp')
                  ->comment('Phân loại sản phẩm: điện tử, thời trang, mỹ phẩm, thực phẩm, sách, khác');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('loai');
        });
    }
};
