<?php
/**
 * SSL Certificate Setup Script
 * 
 * This script helps set up SSL certificates for cURL to work with Supabase.
 */

// URL to download the CA certificate bundle from
$cacertUrl = 'https://curl.se/ca/cacert.pem';
$localPath = __DIR__ . '/lib/cacert.pem';

// Check if we can write to the directory
if (!is_writable(dirname($localPath))) {
    die("Error: Cannot write to " . dirname($localPath) . ". Please check permissions.");
}

// Try to download the CA certificate bundle
echo "Downloading CA certificate bundle...\n";
$cacert = @file_get_contents($cacertUrl);

if ($cacert === false) {
    die("Failed to download CA certificate bundle. Please download it manually from:\n" . 
        $cacertUrl . "\nand save it as: " . $localPath);
}

// Save the certificate file
if (file_put_contents($localPath, $cacert) === false) {
    die("Failed to save CA certificate bundle to " . $localPath);
}

echo "Successfully downloaded and saved CA certificate bundle to " . $localPath . "\n\n";

// Test the connection
$testUrl = 'https://' . parse_url(SUPABASE_URL, PHP_URL_HOST) . '/rest/v1/';

echo "Testing connection to Supabase...\n";
$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . SUPABASE_KEY,
    'Authorization: Bearer ' . SUPABASE_KEY
]);

// Use the downloaded certificate bundle
curl_setopt($ch, CURLOPT_CAINFO, $localPath);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$errno = curl_errno($ch);

if ($error) {
    echo "❌ Connection test failed:\n";
    echo "Error ($errno): $error\n\n";
    
    if ($errno === CURLE_SSL_CACERT || $errno === CURLE_SSL_CACERT_BADFILE) {
        echo "SSL Certificate verification failed.\n";
        echo "Troubleshooting steps:\n";
        echo "1. Try running this script as administrator\n";
        echo "2. Check if your system clock is correct\n";
        echo "3. Try downloading the certificate bundle manually from:\n";
        echo "   $cacertUrl\n";
        echo "   and save it to: $localPath\n\n";
    }
    
    echo "As an alternative, you can disable SSL verification (not recommended for production):\n";
    echo "1. Open: " . __DIR__ . "/lib/supabase.php\n";
    echo "2. Find the line with CURLOPT_SSL_VERIFYPEER\n";
    echo "3. Change it to: curl_setopt(\$ch, CURLOPT_SSL_VERIFYPEER, false);\n";
} else {
    echo "✅ Successfully connected to Supabase! (HTTP $httpCode)\n";
    echo "Response: " . substr($response, 0, 500) . "...\n";
}

curl_close($ch);

// Update php.ini with the CA certificate path
echo "\nTo make this permanent, add the following to your php.ini file:\n";
echo "curl.cainfo = \"" . str_replace('\\', '\\\\', $localPath) . "\"\n";

// Find php.ini files
$phpIniPaths = [
    php_ini_loaded_file(),
    get_cfg_var('cfg_file_path'),
    PHP_CONFIG_FILE_PATH
];

echo "\nCommon php.ini locations to check:\n";
foreach (array_filter(array_unique($phpIniPaths)) as $iniPath) {
    if (file_exists($iniPath)) {
        echo "- $iniPath\n";
    }
}

echo "\nAfter updating php.ini, restart your web server.\n";
