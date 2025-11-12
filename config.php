<?php
// Configuration file

// Supabase Configuration
define('SUPABASE_URL', 'https://thmouwgqfdcvfgovemqg.supabase.co');
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRobW91d2dxZmRjdmZnb3ZlbXFnIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjI3NTEyNTAsImV4cCI6MjA3ODMyNzI1MH0.ncReHS7oBkK4YfzDFbMyyhLU_JtJ8YWwSQwlrIebQdE');

// Admin Configuration
define('ADMIN_EMAIL', 'admin@donation.com'); // Hardcoded admin email
define('ADMIN_PASSWORD', 'admin123'); // Hardcoded admin password (change this!)

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load libraries
require_once __DIR__ . '/lib/supabase.php';
require_once __DIR__ . '/lib/auth.php';

// Initialize Supabase client
$supabase = new Supabase(SUPABASE_URL, SUPABASE_KEY);
?>
