<?php
require_once '../config.php';

$donationId = isset($_GET['donation_id']) ? sanitize($_GET['donation_id']) : '';
$amount = isset($_GET['amount']) ? sanitize($_GET['amount']) : '';

// Validate inputs
if (empty($donationId)) {
    $_SESSION['error'] = 'Invalid donation ID';
    header("Location: donate.php");
    exit;
}

// Store payment info in session
$_SESSION['current_payment'] = [
    'donation_id' => $donationId,
    'amount' => $amount,
    'timestamp' => time()
];

// Your Razorpay.me payment link
$razorpayUrl = 'https://razorpay.me/@solvers';

// Redirect directly to Razorpay
header("Location: " . $razorpayUrl);
exit;
?>
