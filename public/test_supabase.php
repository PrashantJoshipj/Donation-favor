<?php
require_once '../config.php';

header('Content-Type: text/plain');
echo "=== Supabase Connection Test ===\n\n";

// Test database connection
try {
    echo "Testing database connection...\n";
    
    // Test connection by selecting from a table
    $result = $supabase->select('donations', 'limit=1');
    
    if ($result === false) {
        echo "❌ Connection failed: Could not connect to Supabase\n";
        $error = error_get_last();
        if ($error) {
            echo "Error details: " . $error['message'] . "\n";
        }
    } else {
        echo "✅ Successfully connected to Supabase\n";
        
        // Test table structure
        echo "\nChecking donations table structure...\n";
        
        // Test insert
        $testData = [
            'id' => 'test_' . uniqid(),
            'name' => 'Test Donation',
            'phone' => '+911234567890',
            'amount' => 100.00,
            'type' => 'test',
            'status' => 'test',
            'payment_status' => 'test',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $insertResult = $supabase->insert('donations', $testData);
        
        if ($insertResult === false) {
            echo "❌ Failed to insert test record. The table structure might be incorrect.\n";
            
            // Try to get the table structure
            echo "\nAttempting to check table structure...\n";
            
            // Try to select from information_schema
            $tableInfo = $supabase->select('information_schema.columns', "table_name=eq.donations");
            
            if ($tableInfo === false) {
                echo "❌ Could not retrieve table structure. You may need to create the donations table.\n";
                echo "Please run the SQL script at: supabase_setup.sql in your Supabase SQL editor.\n";
            } else {
                echo "✅ Table structure found. Columns:\n";
                foreach ($tableInfo as $column) {
                    echo "- {$column['column_name']} ({$column['data_type']})\n";
                }
            }
        } else {
            echo "✅ Successfully inserted test record\n";
            
            // Clean up test data
            $supabase->delete('donations', "id=eq.{$testData['id']}");
            echo "✅ Cleaned up test data\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test cURL
if (function_exists('curl_version')) {
    echo "\nTesting cURL...\n";
    $ch = curl_init(SUPABASE_URL . '/rest/v1/');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo "❌ cURL Error: " . curl_error($ch) . "\n";
    } else {
        echo "✅ cURL request successful (HTTP $httpCode)\n";
        if ($httpCode >= 400) {
            echo "Response: " . substr($response, 0, 500) . "\n";
        }
    }
    curl_close($ch);
} else {
    echo "\n❌ cURL is not enabled in your PHP installation\n";
}

echo "\n=== Test Complete ===\n";

// Show PHP configuration
echo "\nPHP Version: " . phpversion() . "\n";
echo "cURL Enabled: " . (function_exists('curl_version') ? 'Yes' : 'No') . "\n";
$curlVersion = curl_version();
echo "cURL Version: " . ($curlVersion['version'] ?? 'N/A') . "\n";
echo "SSL Version: " . ($curlVersion['ssl_version'] ?? 'N/A') . "\n";

// Show environment variables
echo "\nEnvironment Variables:\n";
echo "SUPABASE_URL: " . (defined('SUPABASE_URL') ? 'Set' : 'Not Set') . "\n";
echo "SUPABASE_KEY: " . (defined('SUPABASE_KEY') ? 'Set' . (strlen(SUPABASE_KEY) > 10 ? ' (first 10 chars: ' . substr(SUPABASE_KEY, 0, 10) . '...)' : '') : 'Not Set') . "\n";
?>

=== How to Fix Common Issues ===

1. If you see "connection refused" or "could not connect to host":
   - Verify your Supabase URL and API key in config.php
   - Check if your server allows outbound HTTPS connections
   - If you're on localhost, try temporarily disabling your firewall

2. If you see "permission denied" or 401/403 errors:
   - Verify your Supabase API key has the correct permissions
   - Check if Row Level Security (RLS) is enabled on your tables
   - Run the SQL script in supabase_setup.sql in your Supabase SQL editor

3. If you see "table not found" errors:
   - Create the donations table using the SQL in supabase_setup.sql
   - Make sure the table is in the 'public' schema

4. If you see SSL/TLS errors:
   - Update your cURL and OpenSSL libraries
   - Add this to your php.ini: curl.cainfo = "C:\\path\\to\\cacert.pem"
   - Or disable SSL verification (not recommended for production):
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
