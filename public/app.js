// Form Validation Functions

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Password validation (minimum 6 characters)
function validatePassword(password) {
    return password.length >= 6;
}

// Username validation (minimum 3 characters, alphanumeric)
function validateUsername(username) {
    const re = /^[a-zA-Z0-9_]{3,20}$/;
    return re.test(username);
}

// Amount validation (positive number)
function validateAmount(amount) {
    const num = parseFloat(amount);
    return !isNaN(num) && num > 0;
}

// Display error message
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

// Clear error message
function clearError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

// Signup Form Validation
function validateSignupForm() {
    let isValid = true;
    
    // Clear all errors
    clearError('username-error');
    clearError('email-error');
    clearError('password-error');
    clearError('confirm-password-error');
    
    // Get form values
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    // Validate username
    if (!username) {
        showError('username-error', 'Username is required');
        isValid = false;
    } else if (!validateUsername(username)) {
        showError('username-error', 'Username must be 3-20 characters, alphanumeric only');
        isValid = false;
    }
    
    // Validate email
    if (!email) {
        showError('email-error', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    if (!password) {
        showError('password-error', 'Password is required');
        isValid = false;
    } else if (!validatePassword(password)) {
        showError('password-error', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    // Validate confirm password
    if (!confirmPassword) {
        showError('confirm-password-error', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirm-password-error', 'Passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

// Login Form Validation
function validateLoginForm() {
    let isValid = true;
    
    // Clear all errors
    clearError('email-error');
    clearError('password-error');
    
    // Get form values
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Validate email
    if (!email) {
        showError('email-error', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    if (!password) {
        showError('password-error', 'Password is required');
        isValid = false;
    }
    
    return isValid;
}

// Donation Form Validation
function validateDonationForm() {
    let isValid = true;
    
    // Clear all errors
    clearError('amount-error');
    
    // Get form values
    const amount = document.getElementById('amount').value.trim();
    
    // Validate amount
    if (!amount) {
        showError('amount-error', 'Amount is required');
        isValid = false;
    } else if (!validateAmount(amount)) {
        showError('amount-error', 'Please enter a valid amount greater than 0');
        isValid = false;
    }
    
    return isValid;
}

// Admin Login Form Validation
function validateAdminLoginForm() {
    let isValid = true;
    
    // Clear all errors
    clearError('email-error');
    clearError('password-error');
    
    // Get form values
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Validate email
    if (!email) {
        showError('email-error', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    if (!password) {
        showError('password-error', 'Password is required');
        isValid = false;
    }
    
    return isValid;
}

// Format currency
function formatCurrency(amount) {
    return '₹' + parseFloat(amount).toFixed(2);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
}

// Confirm action
function confirmAction(message) {
    return confirm(message);
}
