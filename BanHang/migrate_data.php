<?php

/**
 * Script to migrate data from MySQL to Supabase PostgreSQL
 * Run: php migrate_data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 Starting data migration from MySQL to Supabase...\n\n";

// MySQL connection (old database)
$mysqlConfig = [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'banhang',
    'username' => 'banhang',
    'password' => 'banhang',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

config(['database.connections.mysql_old' => $mysqlConfig]);

// Tables to migrate in order (respecting foreign keys)
$tables = [
    'users',
    'categories',
    'brands',
    'products',
    'product_images',
    'product_variants',
    'addresses',
    'orders',
    'order_items',
    'reviews',
    'promotions',
    'promotion_items',
    'wishlists',
    'notifications',
    'inventory_logs',
    'audit_logs',
];

$totalRecords = 0;
$errors = [];

foreach ($tables as $table) {
    try {
        echo "📦 Migrating table: {$table}...\n";
        
        // Check if table exists in MySQL
        $exists = DB::connection('mysql_old')
            ->select("SHOW TABLES LIKE '{$table}'");
        
        if (empty($exists)) {
            echo "  ⚠️  Table {$table} not found in MySQL, skipping...\n\n";
            continue;
        }
        
        // Get data from MySQL
        $data = DB::connection('mysql_old')
            ->table($table)
            ->get()
            ->toArray();
        
        $count = count($data);
        
        if ($count === 0) {
            echo "  ℹ️  No data in {$table}\n\n";
            continue;
        }
        
        // Convert stdClass objects to arrays
        $data = array_map(function($item) {
            return (array) $item;
        }, $data);
        
        // Insert into PostgreSQL in chunks
        $chunkSize = 100;
        $chunks = array_chunk($data, $chunkSize);
        
        foreach ($chunks as $chunk) {
            DB::table($table)->insert($chunk);
        }
        
        $totalRecords += $count;
        echo "  ✅ Migrated {$count} records from {$table}\n\n";
        
    } catch (\Exception $e) {
        $error = "❌ Error migrating {$table}: " . $e->getMessage();
        echo "  {$error}\n\n";
        $errors[] = $error;
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "📊 Migration Summary:\n";
echo str_repeat('=', 60) . "\n";
echo "✅ Total records migrated: {$totalRecords}\n";
echo "❌ Errors: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
}

echo "\n🎉 Data migration complete!\n";

// Verify data
echo "\n📊 Verification:\n";
foreach (['users', 'products', 'orders', 'categories'] as $table) {
    try {
        $count = DB::table($table)->count();
        echo "  ✓ {$table}: {$count} records\n";
    } catch (\Exception $e) {
        echo "  ✗ {$table}: Error - " . $e->getMessage() . "\n";
    }
}

echo "\n✅ All done! Your database is now on Supabase.\n";
