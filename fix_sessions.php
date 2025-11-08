<?php
/**
 * Fix all PHP files to include session_init.php before session_start()
 */

$baseDir = __DIR__ . '/server/public';
$sessionInitPath = '../drop-files/lib/session_init.php';
$sessionInitPathAdmin = '../../drop-files/lib/session_init.php';

function fixSessionInFile($filePath, $sessionInitInclude) {
    $content = file_get_contents($filePath);
    
    // Check if file calls session_start()
    if (strpos($content, 'session_start()') === false) {
        return false; // No session_start, skip
    }
    
    // Check if already has session_init
    if (strpos($content, 'session_init.php') !== false) {
        return false; // Already fixed
    }
    
    // Check if uses session_start_timeout (custom function)
    if (strpos($content, 'session_start_timeout') !== false) {
        // Replace session_start_timeout include with session_init
        $content = preg_replace(
            '/include\(["\']\.\.\/drop-files\/lib\/session_start_timeout\.php["\']\);/',
            'include("' . $sessionInitInclude . '");',
            $content
        );
        echo "Fixed (timeout): $filePath\n";
        file_put_contents($filePath, $content);
        return true;
    }
    
    // Find the first session_start() call
    $pattern = '/<\?php\s*\n/';
    if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $insertPos = $matches[0][1] + strlen($matches[0][0]);
        
        // Insert session_init include right after <?php
        $before = substr($content, 0, $insertPos);
        $after = substr($content, $insertPos);
        
        $newContent = $before . 'include("' . $sessionInitInclude . '");' . "\n" . $after;
        
        file_put_contents($filePath, $newContent);
        echo "Fixed: $filePath\n";
        return true;
    }
    
    return false;
}

// Fix files in public directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir)
);

$fixed = 0;
$skipped = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        
        // Determine correct session_init path
        if (strpos($filePath, 'admin') !== false) {
            $include = $sessionInitPathAdmin;
        } else {
            $include = $sessionInitPath;
        }
        
        if (fixSessionInFile($filePath, $include)) {
            $fixed++;
        } else {
            $skipped++;
        }
    }
}

echo "\n";
echo "=====================================\n";
echo "Session Fix Complete!\n";
echo "=====================================\n";
echo "Fixed: $fixed files\n";
echo "Skipped: $skipped files\n";
echo "\n";
echo "All PHP files now include session_init.php before session_start()\n";
?>
