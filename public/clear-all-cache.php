<?php
// Script temporaire pour vider TOUS les caches

echo "🧹 Clearing all caches...\n\n";

// 1. OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✅ OPcache cleared\n";
    } else {
        echo "❌ Failed to clear OPcache\n";
    }
} else {
    echo "⚠️  OPcache not available\n";
}

// 2. Realpath cache
clearstatcache(true);
echo "✅ Realpath cache cleared\n";

// 3. Laravel view cache
$viewCachePath = __DIR__ . '/../storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Laravel view cache cleared (" . count($files) . " files)\n";
}

// 4. Laravel cache
$cachePath = __DIR__ . '/../storage/framework/cache/data';
if (is_dir($cachePath)) {
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            unlink($file->getRealPath());
            $count++;
        }
    }
    echo "✅ Laravel cache cleared ($count files)\n";
}

echo "\n✅ All caches cleared successfully!\n";
echo "🔄 Please refresh your browser now.\n";

// Auto-delete this script for security
@unlink(__FILE__);
echo "\n🗑️  This script has been auto-deleted for security.\n";

