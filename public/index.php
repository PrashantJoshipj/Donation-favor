<?php
require_once '../config.php';

// Get total donations
$totalDonations = 0;
$donationCount = 0;

$donations = $supabase->select('donations', 'select=amount');
if ($donations) {
    $donationCount = count($donations);
    foreach ($donations as $donation) {
        $totalDonations += floatval($donation['amount']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Favor - Make a Difference</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav id="mainNav">
        <div class="nav-container">
            <a href="index.php" class="logo-container">
                <img src="images/logo.png" alt="Donation Favor Logo" class="logo-img">
                <span class="logo-text">Donation Favor</span>
            </a>
        </div>
    </nav>
    
    <script>
        // Add scroll effect for navbar
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>

    <div class="hero">
        <h1>Make a Difference Today</h1>
        <p>Your generosity can change lives. Join us in making the world a better place.</p>
        
        <div class="nav-icons">
            <a href="index.php" title="Home">
                <div class="layer">
                    <span><i class="fas fa-home"></i></span>
                    <span><i class="fas fa-home"></i></span>
                    <span><i class="fas fa-home"></i></span>
                    <span><i class="fas fa-home"></i></span>
                </div>
            </a>
            <a href="donate.php" title="Donate Now">
                <div class="layer">
                    <span><i class="fas fa-hand-holding-heart"></i></span>
                    <span><i class="fas fa-hand-holding-heart"></i></span>
                    <span><i class="fas fa-hand-holding-heart"></i></span>
                    <span><i class="fas fa-hand-holding-heart"></i></span>
                </div>
            </a>
            <a href="about.php" title="About Us">
                <div class="layer">
                    <span><i class="fas fa-info-circle"></i></span>
                    <span><i class="fas fa-info-circle"></i></span>
                    <span><i class="fas fa-info-circle"></i></span>
                    <span><i class="fas fa-info-circle"></i></span>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div class="stats">
        <div class="stat-card">
            <h3>₹<?php echo number_format($totalDonations, 2); ?></h3>
            <p>Total Donations</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $donationCount; ?></h3>
            <p>Total Contributions</p>
        </div>
        <div class="stat-card">
            <h3>100%</h3>
            <p>Transparent</p>
        </div>
    </div>

    <section class="donation-section">
        <h1 class="page-title">Donate & Make a Change</h1>
        <p class="page-subtitle">
            Your support helps us make a real difference. No account needed - donate in just a few clicks!
        </p>

        <div class="donation-container">
            <!-- Charitable Donation -->
            <div class="donation-card">
                <div class="card-header">
                    <h3>Charitable Donation</h3>
                </div>
                <div class="card-body">
                    <p class="card-description">Your generous contribution will help provide essential resources and support to those in need, bringing hope to communities.</p>
                    <a href="donate.php?donation=charitable" class="glow-btn">
                        Donate Now
                    </a>
                </div>
            </div>

            <!-- Education Donation -->
            <div class="donation-card">
                <div class="card-header">
                    <h3>Education Donation</h3>
                </div>
                <div class="card-body">
                    <p class="card-description">Help us provide quality education to underprivileged children and empower them for a brighter future.</p>
                    <a href="donate.php?donation=education" class="glow-btn">
                        Donate Now
                    </a>
                </div>
            </div>

            <!-- Medical Donation -->
            <div class="donation-card">
                <div class="card-header">
                    <h3>Medical Donation</h3>
                </div>
                <div class="card-body">
                    <p class="card-description">Support life-saving medical treatments and healthcare services for those who cannot afford them.</p>
                    <a href="donate.php?donation=medical" class="glow-btn">
                        Donate Now
                    </a>
                </div>
            </div>

            <!-- Animal Donation -->
            <div class="donation-card">
                <div class="card-header">
                    <h3>Animal Donation</h3>
                </div>
                <div class="card-body">
                    <p class="card-description">Help us provide care, shelter, and medical attention to abandoned and injured animals.</p>
                    <a href="donate.php?donation=animal" class="glow-btn">
                        Donate Now
                    </a>
                </div>
            </div>

        </div>
    </section>

    <div class="cta-section">
        <h2>Ready to Make an Impact?</h2>
        <p>Your donation makes a real difference. Thank you for your generosity!</p>
        <a href="donate.php" class="glow-btn">Donate Now</a>
    </div>
    </div>

    <script src="app.js"></script>
</body>
</html>
