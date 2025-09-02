<?php
/**
 * System Check for Hotel Insight - Shared Hosting
 * Upload this file to your hosting root to check system compatibility
 */

echo "<h1>🏨 Hotel Insight - System Check</h1>";
echo "<hr>";

// Basic PHP Info
echo "<h2>📋 PHP Information</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Item</th><th>Value</th><th>Status</th></tr>";

$phpVersion = phpversion();
echo "<tr><td>PHP Version</td><td>{$phpVersion}</td><td>";
if (version_compare($phpVersion, '7.4', '>=')) {
    echo "✅ OK (7.4+)";
} elseif (version_compare($phpVersion, '7.2', '>=')) {
    echo "⚠️  Warning (7.2+) - Some features may not work";
} else {
    echo "❌ Error (Need 7.2+)";
}
echo "</td></tr>";

$memoryLimit = ini_get('memory_limit');
echo "<tr><td>Memory Limit</td><td>{$memoryLimit}</td><td>";
if (intval($memoryLimit) >= 128) {
    echo "✅ OK";
} else {
    echo "⚠️  Warning - May cause issues";
}
echo "</td></tr>";

$maxExecutionTime = ini_get('max_execution_time');
echo "<tr><td>Max Execution Time</td><td>{$maxExecutionTime}</td><td>";
if ($maxExecutionTime >= 30) {
    echo "✅ OK";
} else {
    echo "⚠️  Warning - May timeout";
}
echo "</td></tr>";

echo "</table>";

// Required Extensions
echo "<h2>🔧 Required Extensions</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Extension</th><th>Status</th><th>Required</th></tr>";

$requiredExtensions = [
    'pdo_mysql' => 'Database connection',
    'openssl' => 'Security features',
    'mbstring' => 'String handling',
    'tokenizer' => 'Laravel framework',
    'xml' => 'XML processing',
    'ctype' => 'Character type checking',
    'json' => 'JSON processing',
    'bcmath' => 'Precision math',
    'fileinfo' => 'File type detection',
    'curl' => 'HTTP requests (SerpAPI)'
];

foreach ($requiredExtensions as $ext => $description) {
    $loaded = extension_loaded($ext);
    echo "<tr><td>{$ext}</td><td>";
    if ($loaded) {
        echo "✅ Loaded";
    } else {
        echo "❌ Not Loaded";
    }
    echo "</td><td>{$description}</td></tr>";
}

echo "</table>";

// Directory Permissions
echo "<h2>📁 Directory Permissions</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Directory</th><th>Permission</th><th>Writable</th><th>Status</th></tr>";

$directories = [
    'storage' => 'Storage and cache',
    'storage/framework' => 'Framework cache',
    'storage/logs' => 'Log files',
    'bootstrap/cache' => 'Bootstrap cache'
];

foreach ($directories as $dir => $description) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir);
        
        echo "<tr><td>{$dir}</td><td>{$perms}</td><td>";
        if ($writable) {
            echo "✅ Yes";
        } else {
            echo "❌ No";
        }
        echo "</td><td>{$description}</td></tr>";
    } else {
        echo "<tr><td>{$dir}</td><td>N/A</td><td>N/A</td><td>❌ Directory not found</td></tr>";
    }
}

echo "</table>";

// File Check
echo "<h2>📄 Critical Files Check</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File</th><th>Status</th><th>Description</th></tr>";

$criticalFiles = [
    '.env' => 'Environment configuration',
    'config/services.php' => 'Services configuration',
    'app/Services/SerpApiService.php' => 'SerpAPI service',
    'app/Services/HotelDataAggregatorService.php' => 'Data aggregation service',
    'routes/web.php' => 'Web routes',
    'public/.htaccess' => 'Apache configuration'
];

foreach ($criticalFiles as $file => $description) {
    $exists = file_exists($file);
    echo "<tr><td>{$file}</td><td>";
    if ($exists) {
        echo "✅ Exists";
    } else {
        echo "❌ Missing";
    }
    echo "</td><td>{$description}</td></tr>";
}

echo "</table>";

// Database Connection Test
echo "<h2>🗄️ Database Connection Test</h2>";

if (file_exists('.env')) {
    // Simple database test
    try {
        $host = 'localhost'; // Default for shared hosting
        $dbname = 'your_database_name'; // Change this
        $username = 'your_username'; // Change this
        $password = 'your_password'; // Change this
        
        $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
        echo "✅ Database connection successful";
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage();
        echo "<br><small>Update the database credentials in this file to test</small>";
    }
} else {
    echo "⚠️  .env file not found - cannot test database connection";
}

// SerpAPI Test
echo "<h2>🌐 SerpAPI Connection Test</h2>";

$serpApiKey = '4e8aa76b5aed65e0d0e558589264c1becb21e374464353cf70663289c19935b5';
$testUrl = "https://serpapi.com/search?q=test&api_key={$serpApiKey}&engine=google&num=1";

if (extension_loaded('curl')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ SerpAPI connection successful";
    } else {
        echo "❌ SerpAPI connection failed (HTTP {$httpCode})";
    }
} else {
    echo "❌ cURL extension not available - cannot test SerpAPI";
}

// Recommendations
echo "<h2>💡 Recommendations</h2>";

if (version_compare($phpVersion, '7.4', '<')) {
    echo "<p>⚠️  <strong>PHP Version:</strong> Consider upgrading to PHP 7.4+ for better performance</p>";
}

if (intval($memoryLimit) < 128) {
    echo "<p>⚠️  <strong>Memory:</strong> Consider increasing memory limit to 128M+</p>";
}

if (!extension_loaded('curl')) {
    echo "<p>❌ <strong>cURL:</strong> Required for SerpAPI integration</p>";
}

echo "<p>✅ <strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Fix any issues above</li>";
echo "<li>Create .env file with your database credentials</li>";
echo "<li>Set proper folder permissions (755 for storage, bootstrap/cache)</li>";
echo "<li>Test basic Laravel routes</li>";
echo "<li>Test OTA data fetching</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Generated on: " . date('Y-m-d H:i:s') . "</small></p>";
?>
