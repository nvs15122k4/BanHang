<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('loai')->constrained('brands')->nullOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['product_id', 'name']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_url', 2048);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('products')
            ->select(['id', 'sizes', 'anh'])
            ->orderBy('id')
            ->chunkById(100, function ($products) use ($now): void {
                foreach ($products as $product) {
                    $sizes = is_string($product->sizes) ? json_decode($product->sizes, true) : [];

                    foreach (array_unique(is_array($sizes) ? $sizes : []) as $size) {
                        if (! is_string($size) || trim($size) === '') {
                            continue;
                        }

                        DB::table('product_variants')->insertOrIgnore([
                            'product_id' => $product->id,
                            'name' => trim($size),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    if (filled($product->anh)) {
                        DB::table('product_images')->insert([
                            'product_id' => $product->id,
                            'image_url' => $product->anh,
                            'is_primary' => true,
                            'sort_order' => 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
        });

        Schema::dropIfExists('brands');
    }
};
