<?php
require_once '../config.php';

// Check if admin is logged in
if (!isAdmin()) {
    redirect('login.php');
}

// Get all donations with user information
$donations = $supabase->select('donations', 'select=*&order=date.desc');

// Get user details for each donation
$donationsWithUsers = [];
if ($donations) {
    foreach ($donations as $donation) {
        $user = $supabase->findOne('users', 'id=eq.' . urlencode($donation['user_id']));
        $donationsWithUsers[] = [
            'donation' => $donation,
            'user' => $user
        ];
    }
}

// Calculate statistics
$totalDonations = 0;
$totalAmount = 0;
if ($donations) {
    $totalDonations = count($donations);
    foreach ($donations as $donation) {
        $totalAmount += floatval($donation['amount']);
    }
}

// Get all users count
$users = $supabase->select('users');
$totalUsers = $users ? count($users) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Donation Favor</title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">Admin Panel</a>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="export-csv.php">Export CSV</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Manage donations and view statistics</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>₹<?php echo number_format($totalAmount, 2); ?></h3>
                <p>Total Amount Collected</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $totalDonations; ?></h3>
                <p>Total Donations</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="table-container">
            <h3>All Donations</h3>
            
            <?php if ($donationsWithUsers && count($donationsWithUsers) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donationsWithUsers as $item): ?>
                            <?php 
                                $donation = $item['donation'];
                                $user = $item['user'];
                            ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($donation['date'])); ?></td>
                                <td><?php echo $user ? htmlspecialchars($user['username']) : 'Unknown'; ?></td>
                                <td><?php echo $user ? htmlspecialchars($user['email']) : 'Unknown'; ?></td>
                                <td><strong>₹<?php echo number_format($donation['amount'], 2); ?></strong></td>
                                <td><span style="color: #27ae60; font-weight: bold;">Completed</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No donations recorded yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../public/app.js"></script>
</body>
</html>
