<?php
/**
 * Clear PHP OPcache
 * Access this file via browser: yourdomain.com/clear_cache.php
 * Delete this file after use for security
 */

// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared successfully!<br>";
} else {
    echo "❌ OPcache not enabled<br>";
}

// Clear realpath cache
clearstatcache(true);
echo "✅ Realpath cache cleared!<br>";

// Show PHP info
echo "<br>PHP Version: " . phpversion() . "<br>";
echo "OPcache Enabled: " . (function_exists('opcache_get_status') ? 'Yes' : 'No') . "<br>";

if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "OPcache Memory Used: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB<br>";
}

echo "<br><strong>⚠️ DELETE THIS FILE after clearing cache!</strong>";
?>
