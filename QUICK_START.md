# Quick Start Guide - Run Your Donation Website

## ✅ Project Structure (Complete!)

```
Donation-Favor/
├── public/              # Main website pages
│   ├── index.php       # Homepage
│   ├── signup.php      # User registration
│   ├── login.php       # User login
│   ├── donate.php      # Donation form
│   ├── dashboard.php   # User dashboard
│   ├── donate-thanks.php
│   ├── logout.php
│   ├── styles.css      # Main stylesheet
│   └── app.js          # JavaScript validation
├── admin/              # Admin panel
│   ├── login.php       # Admin login
│   ├── index.php       # Admin dashboard
│   ├── users.php       # User management
│   ├── export-csv.php  # Export donations
│   └── logout.php
├── lib/                # Core libraries
│   ├── supabase.php    # Supabase client
│   └── auth.php        # Authentication helpers
├── webhook/            # Payment webhooks
│   └── razorpay.php
└── config.php          # Configuration file
```

## 🚀 How to Run (3 Steps)

### Step 1: Configure Supabase (5 minutes)

1. Follow instructions in `SUPABASE_SETUP.md`
2. Update `config.php` with your credentials:
   ```php
   define('SUPABASE_URL', 'YOUR_SUPABASE_URL');
   define('SUPABASE_KEY', 'YOUR_SUPABASE_KEY');
   define('ADMIN_EMAIL', 'admin@donation.com');
   define('ADMIN_PASSWORD', 'admin123');
   ```

### Step 2: Start AMPPS Server

1. Open AMPPS Control Panel
2. Make sure Apache and MySQL are running (green)
3. Apache should be on port 80 (or your configured port)

### Step 3: Access Website

Open your browser and navigate to:

**Main Website:**
```
http://localhost/Donation-Favor/public/
```

**Admin Panel:**
```
http://localhost/Donation-Favor/admin/login.php
```

## 📱 Available Pages

### Public Pages (User)
- **Homepage**: `http://localhost/Donation-Favor/public/index.php`
- **Sign Up**: `http://localhost/Donation-Favor/public/signup.php`
- **Login**: `http://localhost/Donation-Favor/public/login.php`
- **Donate**: `http://localhost/Donation-Favor/public/donate.php` (requires login)
- **Dashboard**: `http://localhost/Donation-Favor/public/dashboard.php` (requires login)

### Admin Pages
- **Admin Login**: `http://localhost/Donation-Favor/admin/login.php`
- **Admin Dashboard**: `http://localhost/Donation-Favor/admin/index.php` (after login)
- **Users List**: `http://localhost/Donation-Favor/admin/users.php`
- **Export CSV**: `http://localhost/Donation-Favor/admin/export-csv.php`

## 🧪 Test the Website

### Test User Flow:
1. ✅ Go to homepage
2. ✅ Click "Sign Up" and create account
3. ✅ Login with your credentials
4. ✅ View your dashboard
5. ✅ Click "Donate" and make a test donation
6. ✅ Check donation appears in dashboard

### Test Admin Flow:
1. ✅ Go to `/admin/login.php`
2. ✅ Login with admin credentials (from config.php)
3. ✅ View all donations on dashboard
4. ✅ Click "Users" to see registered users
5. ✅ Click "Export CSV" to download donation report

## ⚙️ Default Admin Credentials

```
Email: admin@donation.com
Password: admin123
```

⚠️ **IMPORTANT**: Change these in `config.php` before deploying!

## 🎨 Features Included

✅ User authentication (signup/login)  
✅ Password hashing with PHP password_hash()  
✅ Donation form with payment placeholder  
✅ User dashboard with donation history  
✅ Admin panel with full access  
✅ View all donations and users  
✅ Export donations to CSV  
✅ Responsive design  
✅ Form validation (JavaScript)  
✅ Supabase database integration  

## 🛠️ Common Issues

### Issue: Page not found (404)
**Solution**: Make sure you're accessing:
- `http://localhost/Donation-Favor/public/` (not just `/Donation-Favor/`)

### Issue: "Call to undefined function curl_init()"
**Solution**: Enable cURL in AMPPS:
1. Open AMPPS → Apache → Configuration → php.ini
2. Find `;extension=curl`
3. Remove the `;` to enable it
4. Restart Apache

### Issue: Can't connect to Supabase
**Solution**: 
- Check config.php has correct SUPABASE_URL and SUPABASE_KEY
- Make sure tables are created in Supabase
- Check your internet connection

### Issue: Sessions not working
**Solution**:
- Clear browser cache and cookies
- Make sure session folder has write permissions
- Restart Apache

## 📝 Next Steps

1. **Customize Design**: Edit `public/styles.css`
2. **Add Features**: Extend functionality in PHP files
3. **Payment Integration**: Implement real payment gateway
4. **Email Notifications**: Add email alerts for donations
5. **Deploy Online**: Host on shared hosting or cloud

## 🌐 Deploying to Production

When ready to deploy:
1. Update `config.php` with production Supabase credentials
2. Change admin password to something secure
3. Use HTTPS (SSL certificate)
4. Test all features on production
5. Monitor error logs regularly

## 💡 Tips

- Keep `config.php` secure (never commit to public repos)
- Regularly backup your Supabase database
- Test payment gateway in sandbox mode first
- Monitor donation records in admin panel
- Keep PHP and dependencies updated

---

**🎉 You're all set! Start testing your donation website now!**

For detailed documentation, see `README.md`  
For Supabase setup help, see `SUPABASE_SETUP.md`
