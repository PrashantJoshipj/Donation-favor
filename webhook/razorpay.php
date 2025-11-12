<?php
/**
 * Razorpay Webhook Handler (Optional)
 * 
 * This file handles webhook events from Razorpay payment gateway.
 * Configure this URL in your Razorpay dashboard to receive payment notifications.
 * 
 * Webhook URL: https://yourdomain.com/webhook/razorpay.php
 */

require_once '../config.php';

// Get the webhook payload
$payload = file_get_contents('php://input');
$event = json_decode($payload, true);

// Log webhook for debugging
error_log('Razorpay Webhook: ' . $payload);

// Function to find donation by payment ID or contact info
function findDonationByPayment($payment) {
    global $supabase;
    
    // Try to find by payment ID first
    if (!empty($payment['id'])) {
        $result = $supabase->select('donations', 'transaction_id=eq.' . urlencode($payment['id']));
        if ($result && count($result) > 0) {
            return $result[0];
        }
    }
    
    // Try to find by contact info if available
    if (!empty($payment['contact'])) {
        $result = $supabase->select('donations', 'phone=ilike.%' . urlencode($payment['contact']) . '% ORDER BY created_at DESC LIMIT 1');
        if ($result && count($result) > 0) {
            return $result[0];
        }
    }
    
    return null;
}

// Verify webhook signature (recommended for production)
// You would need your Razorpay webhook secret for this
// $webhookSecret = 'your_webhook_secret';
// $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
// $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
// if ($signature !== $expectedSignature) {
//     http_response_code(400);
//     exit('Invalid signature');
// }

// Process the event
if (isset($event['event'])) {
    switch ($event['event']) {
        case 'payment.captured':
            // Payment successful
            handlePaymentCaptured($event);
            break;
            
        case 'payment.failed':
            // Payment failed
            handlePaymentFailed($event);
            break;
            
        default:
            // Other events
            error_log('Unhandled event: ' . $event['event']);
    }
}

// Send 200 OK response
http_response_code(200);
echo json_encode(['status' => 'ok']);

/**
 * Handle successful payment
 */
function handlePaymentCaptured($event) {
    global $supabase;
    
    $payment = $event['payload']['payment']['entity'] ?? null;
    
    if (!$payment) {
        error_log('No payment data in webhook');
        return;
    }
    
    // Extract payment details
    $paymentId = $payment['id'];
    $amount = $payment['amount'] / 100; // Razorpay sends amount in paise
    $currency = $payment['currency'] ?? 'INR';
    $status = $payment['status'];
    
    // Find existing donation record
    $donation = findDonationByPayment($payment);
    
    if ($donation) {
        // Update existing donation
        $updateData = [
            'payment_status' => 'captured',
            'transaction_id' => $paymentId,
            'amount' => (string)$amount,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Update the donation record
        $result = $supabase->update('donations', $updateData, 'id=eq.' . urlencode($donation['id']));
        
        if ($result) {
            error_log('Donation updated successfully: ' . $donation['id']);
        } else {
            error_log('Failed to update donation: ' . $donation['id']);
        }
    } else {
        // Create new donation record
        $donationData = [
            'id' => uniqid('don_'),
            'name' => $payment['notes']['name'] ?? 'Anonymous Donor',
            'email' => $payment['email'] ?? '',
            'phone' => $payment['contact'] ?? '',
            'amount' => (string)$amount,
            'currency' => $currency,
            'payment_status' => 'captured',
            'transaction_id' => $paymentId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $supabase->insert('donations', $donationData);
        
        if ($result) {
            error_log('New donation recorded successfully: ' . $paymentId);
        } else {
            error_log('Failed to record new donation: ' . $paymentId);
        }
    }
}

/**
 * Handle failed payment
 */
function handlePaymentFailed($event) {
    global $supabase;
    
    $payment = $event['payload']['payment']['entity'] ?? null;
    if (!$payment) {
        error_log('No payment data in failed webhook');
        return;
    }
    
    // Find existing donation
    $donation = findDonationByPayment($payment);
    
    if ($donation) {
        // Update donation status to failed
        $updateData = [
            'payment_status' => 'failed',
            'transaction_id' => $payment['id'] ?? null,
            'error_message' => $payment['error_description'] ?? 'Payment failed',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $supabase->update('donations', $updateData, 'id=eq.' . urlencode($donation['id']));
        
        if ($result) {
            error_log('Payment failed status updated for donation: ' . $donation['id']);
        } else {
            error_log('Failed to update payment status for donation: ' . $donation['id']);
        }
    } else {
        error_log('Payment failed (no matching donation found): ' . $payment['id']);
    }
}
?>
