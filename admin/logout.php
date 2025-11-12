<?php
require_once '../config.php';

// Destroy session and logout
destroySession();

// Redirect to admin login
redirect('login.php');
?>
