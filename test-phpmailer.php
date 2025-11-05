<?php
// Diagnostic script to check PHPMailer installation
// Access this file directly via browser to see what's wrong

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHPMailer Diagnostic Test</h2>";
echo "<pre>";

echo "Current directory: " . getcwd() . "\n";
echo "Script path: " . __DIR__ . "\n";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "\n\n";

// Check vendor/autoload.php
echo "=== Checking vendor/autoload.php ===\n";
if (file_exists('vendor/autoload.php')) {
    echo "✓ vendor/autoload.php exists\n";
    echo "  Full path: " . realpath('vendor/autoload.php') . "\n";
} else {
    echo "✗ vendor/autoload.php NOT FOUND\n";
    echo "  Looking in: " . __DIR__ . "/vendor/autoload.php\n";
}

// Check PHPMailer files (Composer creates vendor/phpmailer/phpmailer/)
echo "\n=== Checking PHPMailer files ===\n";
$phpmailer_files = [
    'vendor/phpmailer/phpmailer/src/PHPMailer.php',  // Standard Composer structure
    'vendor/phpmailer/phpmailer/phpmailer/src/PHPMailer.php',  // Alternative
    'vendor/phpmailer/src/PHPMailer.php'  // Direct
];

foreach ($phpmailer_files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
        echo "  Full path: " . realpath($file) . "\n";
    } else {
        echo "✗ $file NOT FOUND\n";
        echo "  Looking in: " . __DIR__ . "/$file\n";
    }
}

// Try to load autoload
echo "\n=== Testing autoload ===\n";
if (file_exists('vendor/autoload.php')) {
    try {
        require_once 'vendor/autoload.php';
        echo "✓ vendor/autoload.php loaded successfully\n";
        
        // Check if classes exist
        echo "\n=== Checking for PHPMailer classes ===\n";
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            echo "✓ PHPMailer\\PHPMailer\\PHPMailer class exists\n";
        } else {
            echo "✗ PHPMailer\\PHPMailer\\PHPMailer class NOT FOUND\n";
        }
        
        if (class_exists('PHPMailer\PHPMailer\SMTP')) {
            echo "✓ PHPMailer\\PHPMailer\\SMTP class exists\n";
        } else {
            echo "✗ PHPMailer\\PHPMailer\\SMTP class NOT FOUND\n";
        }
        
        if (class_exists('PHPMailer\PHPMailer\Exception')) {
            echo "✓ PHPMailer\\PHPMailer\\Exception class exists\n";
        } else {
            echo "✗ PHPMailer\\PHPMailer\\Exception class NOT FOUND\n";
        }
        
        // Try to manually require (check all possible paths)
        echo "\n=== Trying manual require ===\n";
        $phpmailer_paths = [
            'vendor/phpmailer/phpmailer/src/',  // Standard Composer structure
            'vendor/phpmailer/phpmailer/phpmailer/src/',  // Alternative
            'vendor/phpmailer/src/'  // Direct
        ];
        
        $found_path = null;
        foreach ($phpmailer_paths as $path) {
            if (file_exists($path . 'PHPMailer.php')) {
                $found_path = $path;
                echo "✓ Found PHPMailer at: $path\n";
                require_once $path . 'PHPMailer.php';
                require_once $path . 'SMTP.php';
                require_once $path . 'Exception.php';
                echo "✓ Manually required PHPMailer files\n";
                break;
            }
        }
        
        if (!$found_path) {
            echo "✗ Could not find PHPMailer files in any expected location\n";
        } else {
            // Check again
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                echo "✓ PHPMailer class now exists after manual require\n";
            }
        }
        
        // Try to instantiate
        echo "\n=== Testing instantiation ===\n";
        try {
            use PHPMailer\PHPMailer\PHPMailer;
            $mail = new PHPMailer(true);
            echo "✓ PHPMailer instantiated successfully!\n";
        } catch (Exception $e) {
            echo "✗ Failed to instantiate PHPMailer: " . $e->getMessage() . "\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error loading autoload: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ Cannot test autoload - vendor/autoload.php not found\n";
}

// Check vendor directory structure
echo "\n=== Vendor directory structure ===\n";
if (is_dir('vendor')) {
    echo "vendor/ directory contents:\n";
    $items = scandir('vendor');
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..') {
            $path = 'vendor/' . $item;
            $type = is_dir($path) ? 'DIR' : 'FILE';
            echo "  [$type] $item\n";
        }
    }
    
    if (is_dir('vendor/phpmailer')) {
        echo "\nvendor/phpmailer/ directory contents:\n";
        $items = scandir('vendor/phpmailer');
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                $path = 'vendor/phpmailer/' . $item;
                $type = is_dir($path) ? 'DIR' : 'FILE';
                echo "  [$type] $item\n";
            }
        }
    }
} else {
    echo "✗ vendor/ directory does not exist\n";
}

echo "\n=== Test Complete ===\n";
echo "</pre>";

?>

