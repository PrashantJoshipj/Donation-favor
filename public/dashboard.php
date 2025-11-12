<?php
require_once '../config.php';

// Check if user is logged in
requireLogin('login.php');

// Get user's donations
$donations = $supabase->select('donations', 'user_id=eq.' . urlencode(getCurrentUserId()) . '&order=date.desc');

// Calculate total donated
$totalDonated = 0;
if ($donations) {
    foreach ($donations as $donation) {
        $totalDonated += floatval($donation['amount']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Donation Favor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">Donation Favor</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="donate.php">Donate</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?>!</h1>
            <p>Track your contributions and make a difference</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>₹<?php echo number_format($totalDonated, 2); ?></h3>
                <p>Total Donated</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $donations ? count($donations) : 0; ?></h3>
                <p>Total Donations</p>
            </div>
            <div class="stat-card">
                <h3>Active</h3>
                <p>Account Status</p>
            </div>
        </div>

        <div style="text-align: center; margin: 2rem 0;">
            <a href="donate.php" class="btn btn-primary">Make Another Donation</a>
        </div>

        <div class="table-container">
            <h3>Donation History</h3>
            
            <?php if ($donations && count($donations) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($donation['date'])); ?></td>
                                <td>₹<?php echo number_format($donation['amount'], 2); ?></td>
                                <td><span style="color: #27ae60; font-weight: bold;">Completed</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No donations yet. Be the first to make a difference!</p>
                    <a href="donate.php" class="btn btn-primary" style="margin-top: 1rem;">Donate Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>
