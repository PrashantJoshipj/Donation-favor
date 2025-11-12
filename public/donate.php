<?php
require_once '../config.php';

$error = '';
$success = '';
$donationTypes = [
    'charitable' => 'Charitable Donation',
    'education' => 'Education Donation',
    'medical' => 'Medical Donation',
    'animal' => 'Animal Donation'
];

// Default to first donation type if not specified or invalid
$donationType = isset($_GET['donation']) ? sanitize($_GET['donation']) : 'charitable';
if (!array_key_exists($donationType, $donationTypes)) {
    $donationType = 'charitable';
}
$donationTitle = $donationTypes[$donationType] ?? 'Charitable Donation';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $countryCode = isset($_POST['country_code']) ? sanitize($_POST['country_code']) : '+91';
    $phone = sanitize($_POST['phone']);
    $amount = isset($_POST['amount']) ? sanitize($_POST['amount']) : '';
    $type = isset($_POST['donation_type']) ? sanitize($_POST['donation_type']) : 'charitable';
    // Ensure the donation type is valid
    if (!array_key_exists($type, $donationTypes)) {
        $type = 'charitable';
    }
    
    // Format phone number with country code
    $fullPhone = $countryCode . $phone;
    
    // Server-side validation
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($phone)) $errors[] = 'Phone number is required';
    
    if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Please enter a valid 10-digit phone number';
    }
    
    if ($amount !== '' && (!is_numeric($amount) || floatval($amount) <= 0)) {
        $errors[] = 'Please enter a valid amount greater than 0';
    }
    
    if (empty($errors)) {
        // Prepare donation data for Supabase
        $donationData = [
            'id' => generateId(),
            'name' => $name,
            'email' => '',
            'phone' => $fullPhone,
            'amount' => ($amount !== '') ? floatval($amount) : null,
            'type' => $type,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'payment_status' => 'initiated'
        ];
        
        // Save to Supabase with error handling
        try {
            // Log the data being sent to Supabase
            error_log('Attempting to insert donation data: ' . print_r($donationData, true));
            
            $result = $supabase->insert('donations', $donationData);
            
            if ($result === false) {
                $errorInfo = error_get_last();
                $errorMsg = 'Supabase insert failed';
                if ($errorInfo) {
                    $errorMsg .= ': ' . $errorInfo['message'];
                }
                error_log($errorMsg);
                throw new Exception($errorMsg);
            }
            
            // Store donation data in session for the thank you page
            $_SESSION['donation_data'] = $donationData;
            $_SESSION['donation_id'] = $donationData['id'];
            
            // Redirect to payment processing page
            $cleanAmount = preg_replace('/[^0-9.]/', '', $amount);
            header("Location: payment.php?donation_id=" . urlencode($donationData['id']) . "&amount=" . urlencode($cleanAmount));
            exit;
            
        } catch (Exception $e) {
// Log the full exception with trace
            error_log('Donation processing error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Try to get more detailed error info if available
            $errorDetails = '';
            if (function_exists('error_get_last')) {
                $lastError = error_get_last();
                if ($lastError) {
                    $errorDetails = ' Details: ' . $lastError['message'];
                    error_log('Last PHP error: ' . print_r($lastError, true));
                }
            }
            
            // Check cURL error if any
            if (function_exists('curl_errno')) {
                $curlError = curl_errno($ch) ? curl_error($ch) : 'No cURL error';
                error_log('cURL error: ' . $curlError);
                $errorDetails .= ' cURL: ' . $curlError;
            }
            
            $error = 'Failed to process your donation. Please try again. If the problem persists, please contact support.' . $errorDetails;
            
            // Log the donation data (without sensitive info)
            $logData = $donationData;
            unset($logData['phone']); // Don't log phone numbers
            unset($logData['email']); // Don't log emails in error logs
            error_log('Failed donation data: ' . print_r($logData, true));
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($donationTitle); ?> - Donation Favor</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <style>
        .amount-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }
        
        /* Style for all amount buttons */
        .amount-btn[data-amount] {
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
            color: white;
            border-color: #45a049;
            font-weight: 600;
        }
        
        /* Hover effects for all amount buttons */
        .amount-btn[data-amount]:hover {
            background: linear-gradient(45deg, #45a049, #7CB342);
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .amount-btn {
            padding: 14px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
            position: relative;
            z-index: 1;
            font-size: 15px;
            font-weight: 500;
        }
        .amount-btn:hover, .amount-btn.active {
            background: #333;
            color: white;
            border-color: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        #amount {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            color: #333;
            background-color: #fff;
        }
        #amount:focus {
            border-color: #333;
            outline: none;
            box-shadow: 0 0 0 2px rgba(107, 0, 255, 0.12);
        }
        #amount::placeholder {
            color: #999;
        }
        #amount:-webkit-autofill,
        #amount:-webkit-autofill:hover,
        #amount:-webkit-autofill:focus,
        #amount:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: #333 !important;
        }
    </style>
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

    <div class="hero" style="padding: 2rem; text-align: center;">
        <h2><?php echo htmlspecialchars($donationTitle); ?></h2>
        <p class="subtitle">Your support makes a difference. Fill in your details to proceed with the donation.</p>
        
        <div class="nav-icons" style="margin: 1.5rem 0;">
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
        <div class="form-container">
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form id="donationForm" action="donate.php" method="POST">
                <input type="hidden" name="donation_type" value="<?php echo htmlspecialchars($donationType); ?>">
                
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    <div class="error" id="nameError"></div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <div class="phone-input-container">
                        <select name="country_code" class="country-code" required>
                            <option value="+91" selected>+91 India (IN)</option>
                            <option value="+1">+1 USA/Canada (US/CA)</option>
                            <option value="+44">+44 United Kingdom (UK)</option>
                            <option value="+61">+61 Australia (AUS)</option>
                            <option value="+971">+971 UAE</option>
                            <option value="+966">+966 Saudi Arabia (KSA)</option>
                            <option value="+65">+65 Singapore (SG)</option>
                            <option value="+60">+60 Malaysia (MY)</option>
                            <option value="+63">+63 Philippines (PH)</option>
                            <option value="+66">+66 Thailand (TH)</option>
                            <option value="+81">+81 Japan (JP)</option>
                            <option value="+82">+82 South Korea (KR)</option>
                            <option value="+86">+86 China (CN)</option>
                            <option value="+92">+92 Pakistan (PK)</option>
                            <option value="+93">+93 Afghanistan (AF)</option>
                            <option value="+94">+94 Sri Lanka (LK)</option>
                            <option value="+95">+95 Myanmar (MM)</option>
                            <option value="+880">+880 Bangladesh (BD)</option>
                            <option value="+977">+977 Nepal (NP)</option>
                            <option value="+960">+960 Maldives (MV)</option>
                            <option value="+20">+20 Egypt (EG)</option>
                            <option value="+27">+27 South Africa (ZA)</option>
                        </select>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               pattern="[0-9]{10}" 
                               maxlength="10"
                               inputmode="numeric"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                               required
                               placeholder="Enter your phone number"
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div class="error" id="phoneError"></div>
                </div>
                
                <div class="form-group">
                    <label for="amount">Donation Amount (₹) <span style="font-size: 0.9rem; color: #666;">(optional)</span></label>
                    <div class="amount-buttons">
                        <div class="amount-btn" data-amount="100">₹100</div>
                        <div class="amount-btn" data-amount="500">₹500</div>
                        <div class="amount-btn" data-amount="1000">₹1,000</div>
                        <div class="amount-btn" data-amount="2000">₹2,000</div>
                        <div class="amount-btn" data-amount="5000">₹5,000</div>
                        <div class="amount-btn" data-amount="10000">₹10,000</div>
                        <div class="amount-btn" data-amount="25000">₹25,000</div>
                        <div class="amount-btn" data-amount="50000">₹50,000</div>
                        <div class="amount-btn" data-amount="100000">₹1,00,000</div>
                    </div>
                    <input type="number" id="amount" name="amount" step="100" min="100"
                           placeholder="Or enter custom amount (min ₹100)"
                           value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
                    <div class="error" id="amountError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1.1rem;">
                    Proceed to Payment
                </button>
                
                <div class="payment-methods" style="margin-top: 20px; text-align: center;">
                    <p>Secure payment via Razorpay</p>
                    <img src="https://razorpay.com/assets/razorpay-glyph.svg" alt="Razorpay" style="height: 30px; margin-top: 10px;">
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form elements
            const amountButtons = document.querySelectorAll('.amount-btn');
            const amountInput = document.getElementById('amount');
            const phoneInput = document.getElementById('phone');
            const form = document.getElementById('donationForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Format number with commas
            const formatNumber = (num) => {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            };
            
            // Amount buttons functionality
            amountButtons.forEach(button => {
                // Format button text with commas
                const amount = button.getAttribute('data-amount');
                if (amount) {
                    button.textContent = `₹${formatNumber(amount)}`;
                }
                
                button.addEventListener('click', function() {
                    const amount = this.getAttribute('data-amount');
                    amountInput.value = amount;
                    
                    // Update active state
                    amountButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Handle custom amount input
            amountInput.addEventListener('input', function() {
                // Clear active state when typing
                amountButtons.forEach(btn => btn.classList.remove('active'));
                
                // Format the number as user types
                if (this.value) {
                    const num = this.value.replace(/\D/g, '');
                    this.value = formatNumber(num);
                }
            });
            
            // Phone number validation
            phoneInput.addEventListener('input', function(e) {
                // Allow only numbers and limit to 10 digits
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
                
                // Validate as user types
                validatePhone(this.value);
            });
            
            // Validate phone number
            const validatePhone = (phone) => {
                const phoneError = document.getElementById('phoneError');
                if (phone.length > 0 && (phone.length !== 10 || !/^\d{10}$/.test(phone))) {
                    phoneError.textContent = 'Please enter a valid 10-digit phone number';
                    return false;
                }
                phoneError.textContent = '';
                return true;
            };
            
            // Validate amount
            const validateAmount = (amount) => {
                const amountError = document.getElementById('amountError');
                if (amount && (isNaN(amount) || parseFloat(amount) < 100)) {
                    amountError.textContent = 'Minimum donation amount is ₹100';
                    return false;
                }
                amountError.textContent = '';
                return true;
            };
            
            // Form validation on submit
            form.addEventListener('submit', function(e) {
                const phone = phoneInput.value.trim();
                const amount = amountInput.value.replace(/\D/g, ''); // Remove commas for validation
                let isValid = true;
                
                // Reset errors
                document.querySelectorAll('.error').forEach(el => el.textContent = '');
                
                // Validate required fields
                if (!phone) {
                    document.getElementById('phoneError').textContent = 'Phone number is required';
                    isValid = false;
                } else if (!validatePhone(phone)) {
                    isValid = false;
                }
                
                // Validate amount if provided
                if (amount && !validateAmount(amount)) {
                    isValid = false;
                }
                
                if (isValid) {
                    // Update amount value without commas before submission
                    amountInput.value = amount;
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                } else {
                    e.preventDefault();
                }
            });
            
            // Validate on blur
            phoneInput.addEventListener('blur', () => validatePhone(phoneInput.value));
            amountInput.addEventListener('blur', () => validateAmount(amountInput.value.replace(/\D/g, '')));
        });
    </script>
</body>
</html>
