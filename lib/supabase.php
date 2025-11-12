<?php
// Minimal Supabase REST client via cURL

class Supabase {
    private $url;
    private $key;
    
    public function __construct($url, $key) {
        $this->url = rtrim($url, '/');
        $this->key = $key;
    }
    
    /**
     * Make a request to Supabase REST API
     */
    private function request($endpoint, $method = 'GET', $data = null, $filters = '') {
        $url = $this->url . '/rest/v1/' . $endpoint;
        
        if ($filters) {
            $url .= '?' . $filters;
        }
        
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // SSL/TLS configuration
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        // Try to use system CA bundle if available
        $cacertPath = __DIR__ . '/cacert.pem';
        if (file_exists($cacertPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $cacertPath);
            curl_setopt($ch, CURLOPT_CAPATH, dirname($cacertPath));
        } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // On Windows, try to use the system certificate store
            curl_setopt($ch, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);
        }
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            $errno = curl_errno($ch);
            $errorMsg = "Supabase cURL Error ($errno): " . $error;
            
            // Add more details for common errors
            if ($errno === CURLE_SSL_CACERT || $errno === CURLE_SSL_CACERT_BADFILE) {
                $errorMsg .= "\nSSL Certificate verification failed. " .
                           "Please ensure your PHP installation has a valid CA certificate bundle.";
            }
            
            error_log($errorMsg);
            return false;
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        error_log("Supabase HTTP Error ($httpCode): " . $response);
        return false;
    }
    
    /**
     * Insert data into table
     */
    public function insert($table, $data) {
        return $this->request($table, 'POST', $data);
    }
    
    /**
     * Select data from table with optional filters
     */
    public function select($table, $filters = '') {
        return $this->request($table, 'GET', null, $filters);
    }
    
    /**
     * Update data in table
     */
    public function update($table, $data, $filters = '') {
        return $this->request($table, 'PATCH', $data, $filters);
    }
    
    /**
     * Delete data from table
     */
    public function delete($table, $filters = '') {
        return $this->request($table, 'DELETE', null, $filters);
    }
    
    /**
     * Find one record by filter
     */
    public function findOne($table, $filters = '') {
        $result = $this->select($table, $filters);
        if ($result && is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }
}
?>
