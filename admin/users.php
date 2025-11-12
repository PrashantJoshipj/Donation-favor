<?php
require_once '../config.php';

// Check if admin is logged in
requireAdmin('login.php');

// Get all users
$users = $supabase->select('users', 'select=*&order=id.desc');

// Get donation count and total for each user
$usersWithStats = [];
if ($users) {
    foreach ($users as $user) {
        $userDonations = $supabase->select('donations', 'user_id=eq.' . urlencode($user['id']));
        
        $donationCount = 0;
        $totalDonated = 0;
        
        if ($userDonations) {
            $donationCount = count($userDonations);
            foreach ($userDonations as $donation) {
                $totalDonated += floatval($donation['amount']);
            }
        }
        
        $usersWithStats[] = [
            'user' => $user,
            'donation_count' => $donationCount,
            'total_donated' => $totalDonated
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Dashboard</title>
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
            <h1>Registered Users</h1>
            <p>Manage user accounts and view their donation history</p>
        </div>

        <div class="table-container">
            <h3>All Users (<?php echo $usersWithStats ? count($usersWithStats) : 0; ?>)</h3>
            
            <?php if ($usersWithStats && count($usersWithStats) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Total Donations</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersWithStats as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['user']['username']); ?></td>
                                <td><?php echo htmlspecialchars($item['user']['email']); ?></td>
                                <td><?php echo $item['donation_count']; ?></td>
                                <td><strong>₹<?php echo number_format($item['total_donated'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No users registered yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../public/app.js"></script>
</body>
</html>
