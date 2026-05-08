<?php
/**
 * Deployment Diagnostic Tool
 * 
 * This script helps diagnose why changes aren't showing on your live site
 * Upload to your cPanel and access via: yourdomain.com/diagnose.php
 * 
 * ⚠️ DELETE THIS FILE after diagnosing for security!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deployment Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        .panel { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        h2 { color: #1a56db; margin-top: 0; border-bottom: 2px solid #1a56db; padding-bottom: 10px; }
        .success { color: #16a34a; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .warning { color: #ea580c; font-weight: bold; }
        .info { background: #eff6ff; padding: 10px; border-left: 4px solid #1a56db; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        td:first-child { font-weight: bold; width: 200px; }
        .btn { background: #1a56db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #1344b8; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔍 Deployment Diagnostic Report</h1>
    <div class="info">
        <strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
        <strong>⚠️ Remember to DELETE this file after use!</strong>
    </div>

    <!-- PHP Environment -->
    <div class="panel">
        <h2>1. PHP Environment</h2>
        <table>
            <tr>
                <td>PHP Version</td>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td>Server Software</td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
            </tr>
            <tr>
                <td>Document Root</td>
                <td><code><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></code></td>
            </tr>
            <tr>
                <td>Current Directory</td>
                <td><code><?php echo __DIR__; ?></code></td>
            </tr>
            <tr>
                <td>Script Filename</td>
                <td><code><?php echo $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown'; ?></code></td>
            </tr>
        </table>
    </div>

    <!-- OPcache Status -->
    <div class="panel">
        <h2>2. OPcache Status (Most Common Issue!)</h2>
        <?php if (function_exists('opcache_get_status')): ?>
            <?php $opcache = opcache_get_status(); ?>
            <table>
                <tr>
                    <td>OPcache Enabled</td>
                    <td class="success">✅ YES</td>
                </tr>
                <tr>
                    <td>Cache Full</td>
                    <td><?php echo $opcache['cache_full'] ? '<span class="error">⚠️ YES - Cache is full!</span>' : '<span class="success">✅ NO</span>'; ?></td>
                </tr>
                <tr>
                    <td>Memory Used</td>
                    <td><?php echo round($opcache['memory_usage']['used_memory'] / 1024 / 1024, 2); ?> MB / <?php echo round($opcache['memory_usage']['free_memory'] / 1024 / 1024, 2); ?> MB free</td>
                </tr>
                <tr>
                    <td>Cached Scripts</td>
                    <td><?php echo $opcache['opcache_statistics']['num_cached_scripts']; ?></td>
                </tr>
                <tr>
                    <td>Hits / Misses</td>
                    <td><?php echo number_format($opcache['opcache_statistics']['hits']); ?> / <?php echo number_format($opcache['opcache_statistics']['misses']); ?></td>
                </tr>
            </table>
            <div class="info" style="margin-top: 15px;">
                <strong>💡 Solution:</strong> OPcache is caching your old PHP files! 
                <a href="clear_cache.php" class="btn">Clear OPcache Now</a>
            </div>
        <?php else: ?>
            <p class="success">✅ OPcache is not enabled (caching not the issue)</p>
        <?php endif; ?>
    </div>

    <!-- Git Status -->
    <div class="panel">
        <h2>3. Git Repository Status</h2>
        <?php
        $gitExists = is_dir(__DIR__ . '/.git');
        if ($gitExists) {
            echo '<p class="success">✅ Git repository detected</p>';
            
            // Get current commit
            $currentCommit = shell_exec('git log -1 --oneline 2>&1');
            echo '<table>';
            echo '<tr><td>Current Commit</td><td><code>' . htmlspecialchars(trim($currentCommit)) . '</code></td></tr>';
            
            // Get branch
            $branch = shell_exec('git rev-parse --abbrev-ref HEAD 2>&1');
            echo '<tr><td>Current Branch</td><td><code>' . htmlspecialchars(trim($branch)) . '</code></td></tr>';
            
            // Check if up to date
            $status = shell_exec('git status -uno 2>&1');
            if (strpos($status, 'up to date') !== false || strpos($status, 'up-to-date') !== false) {
                echo '<tr><td>Status</td><td class="success">✅ Up to date with origin</td></tr>';
            } else {
                echo '<tr><td>Status</td><td class="warning">⚠️ May not be up to date</td></tr>';
            }
            echo '</table>';
            
            echo '<div class="info" style="margin-top: 15px;">';
            echo '<strong>Expected Latest Commit:</strong> <code>a64436f Polish landing page...</code><br>';
            echo '<strong>Does it match?</strong> ';
            if (strpos($currentCommit, 'a64436f') !== false) {
                echo '<span class="success">✅ YES - Files are updated!</span>';
            } else {
                echo '<span class="error">❌ NO - Files NOT updated! Run: git pull origin master</span>';
            }
            echo '</div>';
        } else {
            echo '<p class="error">❌ No Git repository found in current directory</p>';
            echo '<p>This means files were probably uploaded manually or git is in a different directory.</p>';
        }
        ?>
    </div>

    <!-- File Check -->
    <div class="panel">
        <h2>4. Critical File Checks</h2>
        <?php
        $filesToCheck = [
            'app/views/home.php',
            'public/index.php',
            'config/db.php',
            '.htaccess'
        ];
        
        echo '<table>';
        foreach ($filesToCheck as $file) {
            $fullPath = __DIR__ . '/' . $file;
            $exists = file_exists($fullPath);
            echo '<tr>';
            echo '<td><code>' . htmlspecialchars($file) . '</code></td>';
            if ($exists) {
                $modified = filemtime($fullPath);
                $age = time() - $modified;
                $ageStr = $age < 3600 ? round($age / 60) . ' mins ago' : round($age / 3600) . ' hours ago';
                echo '<td class="success">✅ Exists (Modified: ' . $ageStr . ')</td>';
            } else {
                echo '<td class="error">❌ Not Found</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    </div>

    <!-- File Permissions -->
    <div class="panel">
        <h2>5. File Permissions</h2>
        <?php
        $testFile = __DIR__ . '/app/views/home.php';
        if (file_exists($testFile)) {
            $perms = fileperms($testFile);
            $octal = substr(sprintf('%o', $perms), -4);
            echo '<table>';
            echo '<tr><td>home.php Permissions</td><td><code>' . $octal . '</code> ';
            if ($octal == '0644' || $octal == '0664') {
                echo '<span class="success">✅ Correct</span>';
            } else {
                echo '<span class="warning">⚠️ Should be 644</span>';
            }
            echo '</td></tr>';
            echo '</table>';
        }
        ?>
    </div>

    <!-- Recommendations -->
    <div class="panel">
        <h2>6. Recommended Actions</h2>
        <ol style="line-height: 2;">
            <li>Clear OPcache: <a href="clear_cache.php" class="btn">Clear Now</a></li>
            <li>Clear browser cache: Press <code>Ctrl + Shift + Delete</code> or <code>Ctrl + F5</code></li>
            <li>Try accessing in Incognito/Private browsing mode</li>
            <li>If using Cloudflare: Purge cache in Cloudflare dashboard</li>
            <li>Check if document root points to <code>/public</code> folder</li>
            <li>If nothing works, restart PHP-FPM: Run <code>killall -9 php-fpm</code> in Terminal</li>
        </ol>
    </div>

    <!-- Cache Test -->
    <div class="panel">
        <h2>7. Cache Test</h2>
        <p>This timestamp changes every time the page is regenerated (not cached):</p>
        <div style="background: #f3f4f6; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 18px;">
            <?php echo microtime(true); ?>
        </div>
        <p style="margin-top: 10px;">
            <button class="btn" onclick="location.reload();">Refresh Page</button>
            If the timestamp doesn't change, PHP is being cached!
        </p>
    </div>

    <div class="info">
        <strong>🔒 SECURITY WARNING:</strong> Delete this file (<code>diagnose.php</code>) immediately after diagnosing!
    </div>
</body>
</html>
