<?php
require_once '../config.php';

// Get total donations for the stats
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
    <title>About Us - Donation Favor</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    <main class="main-content">
    <div class="hero" style="padding: 3rem 2rem; text-align: center; background: linear-gradient(45deg, #00f2fe 10%, #cc2366 40%, #28f7ac 80%); margin: 6rem 2rem 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); color: #fff;">
        <h1>About Our Team</h1>
        
        <div class="nav-icons" style="margin-top: 2rem;">
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
    
    <div class="container">
        <section class="about-section">
            
            <div class="team-container">
                <!-- Team Member 1 -->
                <div class="team-member">
                    <div class="profile-circle">
                        <img src="/images/Member%201.jpg" alt="Rudra Sharma - Team Member">
                    </div>
                    <h3>Rudra Sharma</h3>
                    <p>EN - 2024/18134</p> 
                </div>
                
                <!-- Team Member 2 -->
                <div class="team-member">
                    <div class="profile-circle">
                        <img src="/images/Member%202.jpg" alt="Prashant Joshi - Team Member">
                    </div>
                    <h3>Prashant Joshi</h3>
                    <p>EN - 2024/19723</p>
                </div>
                
                <!-- Team Member 3 -->
                <div class="team-member">
                    <div class="profile-circle">
                        <img src="/images/Member%203.jpg" alt="Rahul Verma - Team Member">
                    </div>
                    <h3>Rahul Verma</h3>
                    <p>EN - 2024/18542</p>
                </div>
            </div>
            
            <div class="university-section">
                <h2 class="university-title">For Poornima University</h2>
                <div class="university-logo">
                    <img src="data:image/jpeg;base64,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" alt="Poornima University">
                </div>
            </div>
        </section>
    </div>
    
    <style>
        /* Add smooth scrolling to the page */
        html {
            scroll-behavior: smooth;
        }
        
        /* Improve accessibility for focus states */
        a:focus, button:focus {
            outline: 3px solid #6b00ff;
            outline-offset: 3px;
        }
        .page-title {
            text-align: center;
            color: #2d3748;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .team-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 3rem;
            margin: 4rem 0;
        }
        
        .team-member {
            text-align: center;
            transition: transform 0.3s ease;
            flex: 0 0 250px;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .profile-circle {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1.5rem;
            border: 5px solid #6b00ff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
        }
        
        .team-member:hover .profile-circle {
            box-shadow: 0 15px 30px rgba(107, 0, 255, 0.15);
            transform: scale(1.03);
        }
        
        .profile-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
        }
        
        .team-member:hover .profile-circle img {
            transform: scale(1.1);
        }
        
        .team-member h3 {
            font-size: 1.5rem;
            color: #2d3748;
            margin: 1rem 0 0.3rem;
        }
        
        .team-member p {
            color: #718096;
            font-size: 1rem;
        }
        
        .university-section {
            text-align: center;
            margin: 6rem 0 4rem;
            padding: 3rem 0;
            border-top: 1px solid #e2e8f0;
        }
        
        .university-title {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 2rem;
            font-weight: 700;
        }
        
        .university-logo {
            max-width: 300px;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .university-logo img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        @media (max-width: 900px) {
            .team-container {
                gap: 2rem;
            }
            
            .team-member {
                flex: 0 0 200px;
            }
            
            .profile-circle {
                width: 150px;
                height: 150px;
            }
        }
        
        @media (max-width: 600px) {
            .team-container {
                flex-direction: column;
                align-items: center;
                gap: 3rem;
            }
            
            .team-member {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>

    <div class="footer-overlay">
        <div class="footer-text">
            <h2>Join Us in Making a Difference</h2>
            <p> At Donation Favor, we believe in the power of collective giving.</p>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Donation Favor</h3>
                <p>Making a difference, one donation at a time.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="donate.php">Donate Now</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p><i class="fas fa-envelope"></i> info@poornima.edu.in</p>
                <p><i class="fas fa-map-marker-alt"></i>Poornima University, Jaipur</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Donation Favor. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
