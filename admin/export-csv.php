<?php
require_once '../config.php';

// Check if admin is logged in
requireAdmin('login.php');

// Get all donations with user information
$donations = $supabase->select('donations', 'select=*&order=date.desc');

// Check if export is requested
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=donations_' . date('Y-m-d') . '.csv');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, ['Date', 'Username', 'Email', 'Amount', 'Status']);
    
    // Add data rows
    if ($donations) {
        foreach ($donations as $donation) {
            $user = $supabase->findOne('users', 'id=eq.' . urlencode($donation['user_id']));
            
            fputcsv($output, [
                date('Y-m-d H:i:s', strtotime($donation['date'])),
                $user ? $user['username'] : 'Unknown',
                $user ? $user['email'] : 'Unknown',
                $donation['amount'],
                'Completed'
            ]);
        }
    }
    
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Donations - Admin Dashboard</title>
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
            <h1>Export Donations</h1>
            <p>Download donation records as CSV file</p>
        </div>

        <div class="card" style="text-align: center;">
            <h3>Download Donation Report</h3>
            <p style="margin: 2rem 0; color: #666;">
                Click the button below to download all donation records in CSV format.<br>
                Total Records: <strong><?php echo $donations ? count($donations) : 0; ?></strong>
            </p>
            
            <a href="export-csv.php?download=true" class="btn btn-primary">
                Download CSV File
            </a>
            
            <div style="margin-top: 2rem;">
                <a href="index.php" class="btn">Back to Dashboard</a>
            </div>
        </div>

        <div class="table-container">
            <h3>Preview - Recent Donations</h3>
            
            <?php if ($donations && count($donations) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Username</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Show only first 10 for preview
                        $previewDonations = array_slice($donations, 0, 10);
                        foreach ($previewDonations as $donation): 
                            $user = $supabase->findOne('users', 'id=eq.' . urlencode($donation['user_id']));
                        ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($donation['date'])); ?></td>
                                <td><?php echo $user ? htmlspecialchars($user['username']) : 'Unknown'; ?></td>
                                <td>₹<?php echo number_format($donation['amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($donations) > 10): ?>
                    <p style="text-align: center; margin-top: 1rem; color: #666;">
                        Showing 10 of <?php echo count($donations); ?> donations. Download CSV for full list.
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>No donations to export.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../public/app.js"></script>
</body>
</html>
