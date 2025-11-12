<?php
require_once '../config.php';

// Check Supabase connection and table structure
echo "<h1>Supabase Connection Test</h1>";

try {
    // Test connection by fetching donations table structure
    $response = $supabase->request('donations?select=*&limit=1', 'GET');
    
    if ($response === false) {
        echo "<div style='color: red;'>❌ Failed to connect to Supabase or table doesn't exist</div>";
        
        // Try to create the table if it doesn't exist
        echo "<h3>Creating donations table...</h3>";
        $createTable = "
        CREATE TABLE IF NOT EXISTS donations (
            id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            type TEXT NOT NULL,
            status TEXT DEFAULT 'pending',
            payment_status TEXT DEFAULT 'initiated',
            created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
            updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
        );";
        
        // Execute the SQL to create the table
        $result = $supabase->request('rpc/execute_sql', 'POST', [
            'query' => $createTable
        ]);
        
        if ($result) {
            echo "<div style='color: green;'>✅ Successfully created donations table</div>";
        } else {
            echo "<div style='color: red;'>❌ Failed to create donations table</div>";
        }
    } else {
        echo "<div style='color: green;'>✅ Successfully connected to Supabase</div>";
        echo "<h3>Table Structure:</h3>";
        echo "<pre>" . print_r($response, true) . "</pre>";
    }
    
    // Test insert
    echo "<h3>Testing insert operation...</h3>";
    $testData = [
        'name' => 'Test Donor',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'amount' => 10.50,
        'type' => 'test',
        'status' => 'test',
        'payment_status' => 'test'
    ];
    
    $insertResult = $supabase->insert('donations', $testData);
    
    if ($insertResult) {
        $testId = $insertResult[0]['id'] ?? null;
        echo "<div style='color: green;'>✅ Successfully inserted test record. ID: " . htmlspecialchars($testId) . "</div>";
        
        // Clean up
        if ($testId) {
            $deleteResult = $supabase->delete('donations', "id=eq.$testId");
            if ($deleteResult !== false) {
                echo "<div style='color: green;'>✅ Successfully cleaned up test record</div>";
            } else {
                echo "<div style='color: orange;'>⚠️ Could not clean up test record (you may need to delete it manually)</div>";
            }
        }
    } else {
        echo "<div style='color: red;'>❌ Failed to insert test record</div>";
        
        // Check for common issues
        echo "<h3>Troubleshooting:</h3>";
        echo "<ol>";
        echo "<li>Check if the 'donations' table exists in your Supabase database</li>";
        echo "<li>Verify that the table has the correct columns matching the data being inserted</li>";
        echo "<li>Check the browser's developer console for any CORS or network errors</li>";
        echo "<li>Verify your Supabase URL and API key in config.php</li>";
        echo "<li>Check Supabase dashboard logs for any errors</li>";
        echo "</ol>";
        
        // Show last error if available
        if (function_exists('error_get_last')) {
            $lastError = error_get_last();
            if ($lastError) {
                echo "<div style='background: #ffeeee; padding: 10px; margin: 10px 0;'>";
                echo "<strong>Last Error:</strong><br>";
                echo htmlspecialchars(print_r($lastError, true));
                echo "</div>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Show PHP info for debugging
echo "<h3>PHP Info:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>cURL Enabled: " . (function_exists('curl_version') ? 'Yes' : 'No') . "</p>";

// Show environment variables (without sensitive data)
echo "<h3>Environment:</h3>";
$envVars = [
    'HTTP_HOST', 'SERVER_NAME', 'REQUEST_METHOD', 'REMOTE_ADDR',
    'HTTP_USER_AGENT', 'SERVER_PROTOCOL'
];

echo "<table border='1' cellpadding='5'>";
foreach ($envVars as $var) {
    echo "<tr><th>$var</th><td>" . ($_SERVER[$var] ?? 'Not Set') . "</td></tr>";
}
echo "</table>";
?>

<h3>Next Steps:</h3>
<ol>
    <li>Check if the donations table exists in your Supabase dashboard</li>
    <li>Verify that the table has the required columns (name, phone, amount, type, status, payment_status, created_at)</li>
    <li>Check the browser's developer console for any JavaScript errors</li>
    <li>Look for any errors in your PHP error log</li>
</ol>
