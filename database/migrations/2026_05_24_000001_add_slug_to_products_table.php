<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('ten_sp');
        });

        $usedSlugs = [];

        DB::table('products')
            ->select(['id', 'ten_sp'])
            ->orderBy('id')
            ->chunkById(500, function ($products) use (&$usedSlugs): void {
                foreach ($products as $product) {
                    $baseSlug = Str::slug($product->ten_sp) ?: 'san-pham';
                    $slug = $baseSlug;
                    $suffix = 2;

                    while (isset($usedSlugs[$slug])) {
                        $slug = $baseSlug.'-'.$suffix;
                        $suffix++;
                    }

                    $usedSlugs[$slug] = true;

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['slug' => $slug]);
                }
            });

        Schema::table('products', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
