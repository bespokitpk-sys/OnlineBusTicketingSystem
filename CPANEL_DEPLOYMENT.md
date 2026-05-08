# cPanel Deployment Guide - Bus Ticketing System

## 🚀 Quick Deployment Steps

### Step 1: Prepare Your Files
Before uploading to cPanel:
1. Download/export your project
2. Remove development files:
   - `.git/` folder (optional)
   - `node_modules/` (if any)
   - Any `.env` or config with local credentials

### Step 2: Upload to cPanel

#### Option A: File Manager (Recommended for Beginners)
1. Login to cPanel
2. Go to **File Manager**
3. Navigate to `public_html/`
4. Upload your zipped project file
5. Right-click → **Extract**
6. Move contents to desired location

#### Option B: FTP Upload
1. Use FileZilla or any FTP client
2. Connect using cPanel FTP credentials
3. Upload to `/public_html/`

**Recommended Structure:**
```
public_html/
├── BusTicketingSystem/    (or your folder name)
    ├── public/            ← Set this as document root
    ├── app/
    ├── config/
    ├── uploads/
    └── ...
```

### Step 3: Create MySQL Database

1. **Go to**: cPanel → MySQL® Databases
2. **Create Database**:
   - Database Name: `bus_ticketing` (will become `cpanelusername_bus_ticketing`)
   - Click "Create Database"
3. **Create User**:
   - Username: `bus_user` (will become `cpanelusername_bus_user`)
   - Password: Generate strong password (save it!)
   - Click "Create User"
4. **Add User to Database**:
   - Select user and database
   - Grant ALL PRIVILEGES
   - Click "Make Changes"

**Note Down These Credentials:**
```
Host: localhost
Database: cpanelusername_bus_ticketing
Username: cpanelusername_bus_user
Password: [your generated password]
```

### Step 4: Import Database

1. **Go to**: cPanel → phpMyAdmin
2. **Select** your database (cpanelusername_bus_ticketing)
3. **Click** Import tab
4. **Choose** `database.sql` file
5. **Click** Go/Import

**Verify Import:**
- Check that 4 tables exist: users, buses, schedules, tickets
- Verify sample data in buses and schedules tables

### Step 5: Configure Database Connection

1. **Open**: cPanel File Manager
2. **Navigate**: to `config/db.php`
3. **Edit** (right-click → Edit)
4. **Update** credentials:

```php
$dbHost = 'localhost';
$dbUser = 'cpanelusername_bus_user';       // Replace with your username
$dbPass = 'your_generated_password';        // Replace with your password
$dbName = 'cpanelusername_bus_ticketing';  // Replace with your database name
```

5. **Save** changes

### Step 6: Set Document Root (Important!)

You have two options:

#### Option A: Change Domain Document Root (Recommended)
1. **Go to**: cPanel → Domains
2. **Click** on your domain
3. **Change** Document Root to: `public_html/BusTicketingSystem/public`
4. **Save**

#### Option B: Use .htaccess Redirect
If you can't change document root, the existing `.htaccess` in project root will redirect to `public/`

### Step 7: Set File Permissions

In cPanel File Manager:

1. **Select** `uploads/` folder → Permissions → **755**
2. **Select** `uploads/profiles/` → Permissions → **755**
3. **Select** `config/db.php` → Permissions → **644**
4. **Select** all PHP files → Permissions → **644**

Or via SSH/Terminal:
```bash
cd public_html/BusTicketingSystem
chmod 755 uploads uploads/profiles
chmod 644 config/db.php
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

### Step 8: Run Setup Script

1. **Visit**: `https://yourdomain.com/setup.php`
2. The script will:
   - Verify database connection
   - Check tables exist
   - Create default admin account
   - Create test operator and passenger accounts
3. **Copy** the credentials shown on screen

**Default Accounts Created:**
- Admin: `admin@busticketing.com` / `Admin@123`
- Operator: `operator@busticketing.com` / `Operator@123`
- Passengers: Self-register via registration page (no default account)

### Step 9: Security Steps (Critical!)

1. **Delete Setup Files**:
   ```
   setup.php
   diagnose.php
   clear_cache.php
   ```

2. **Change Default Passwords**:
   - Login to each account
   - Go to profile/settings
   - Update password

3. **Enable HTTPS** (if available):
   - Go to cPanel → SSL/TLS
   - Install free SSL certificate (Let's Encrypt)
   - Force HTTPS redirect

### Step 10: Test Your Application

1. **Visit**: `https://yourdomain.com`
2. **Test Login**: Use admin credentials
3. **Verify**:
   - ✓ Can login successfully
   - ✓ Dashboard loads
   - ✓ Can add buses
   - ✓ Can create schedules
   - ✓ Passenger can search and book

---

## 🔧 Troubleshooting Common Issues

### Issue 1: 500 Internal Server Error

**Causes:**
- Incorrect file permissions
- .htaccess syntax error
- PHP version incompatibility

**Solutions:**
1. Check error logs: cPanel → Errors
2. Verify .htaccess is correct
3. Check PHP version (needs 7.4+):
   - cPanel → Select PHP Version
   - Set to 7.4 or 8.0

### Issue 2: Database Connection Failed

**Solutions:**
1. Verify credentials in `config/db.php`
2. Check database user has correct privileges
3. Confirm database name includes cPanel username prefix
4. Test connection in phpMyAdmin

### Issue 3: 404 Not Found on All Pages

**Solutions:**
1. Enable mod_rewrite in .htaccess
2. Check document root points to `public/`
3. Verify .htaccess exists in both root and public/

### Issue 4: Can't Login / Credentials Not Working

**Solutions:**
1. Run `setup.php` again
2. Check users table has entries:
   ```sql
   SELECT * FROM users WHERE role = 'admin';
   ```
3. Manually reset password (see SETUP.md)

### Issue 5: File Upload Not Working

**Solutions:**
1. Check `uploads/` directory exists
2. Set permissions to 755:
   ```bash
   chmod 755 uploads uploads/profiles
   ```
3. Verify PHP `upload_max_filesize` is adequate (2MB+)

### Issue 6: Session Not Working

**Solutions:**
1. Verify PHP sessions are enabled
2. Check session save path is writable
3. Clear browser cookies
4. Check `.htaccess` session settings

---

## 📊 cPanel Specific Settings

### PHP Configuration (if needed)

Go to cPanel → Select PHP Version → Options

Recommended Settings:
```
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
session.save_path = /tmp
```

### Email Configuration

For production email (OTP, password reset):

1. Create email account in cPanel → Email Accounts
2. Update `app/controllers/AuthController.php`:

```php
// Replace test email functions with SMTP
use PHPMailer\PHPMailer\PHPMailer;

private static function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'mail.yourdomain.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply@yourdomain.com';
    $mail->Password = 'your_email_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('noreply@yourdomain.com', 'Bus Ticketing');
    $mail->addAddress($email);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is: $otp";
    
    return $mail->send();
}
```

---

## 🔐 Security Hardening

### 1. Protect Sensitive Directories

Create `.htaccess` in `config/` folder:
```apache
Order deny,allow
Deny from all
```

### 2. Disable Directory Listing

Already done in main `.htaccess`:
```apache
Options -Indexes
```

### 3. Hide PHP Version

In `.htaccess`:
```apache
Header unset X-Powered-By
ServerSignature Off
```

### 4. Enable HTTPS Only

Force HTTPS redirect in `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 5. Regular Backups

Setup automated backups in cPanel:
- Go to cPanel → Backup
- Enable automatic daily backups
- Download monthly full backups

---

## ✅ Pre-Launch Checklist

Before going live:

- [ ] Database imported successfully
- [ ] All credentials updated in config/db.php
- [ ] Document root set correctly
- [ ] File permissions configured (755/644)
- [ ] setup.php run successfully
- [ ] Default passwords changed
- [ ] setup.php deleted
- [ ] HTTPS enabled and forced
- [ ] Email functionality tested
- [ ] Test booking flow works
- [ ] Error logging enabled
- [ ] Backups configured
- [ ] All diagnostic files deleted

---

## 📞 Quick Reference

### Access URLs
- **Homepage**: `https://yourdomain.com/`
- **Admin Login**: `https://yourdomain.com/login`
- **Passenger Dashboard**: `https://yourdomain.com/passenger/dashboard`
- **Operator Dashboard**: `https://yourdomain.com/operator/dashboard`

### Default Credentials (Change After First Login!)
```
Admin:     admin@busticketing.com / Admin@123
Operator:  operator@busticketing.com / Operator@123
Passenger: Self-register via /register page
```

### Important Files
```
config/db.php           - Database configuration
setup.php              - One-time setup (DELETE after use!)
database.sql           - Database schema
.htaccess             - Apache configuration
SETUP.md              - Detailed setup guide
```

---

## 🎉 Deployment Complete!

Your Bus Ticketing System should now be live on cPanel. 

**Next Steps:**
1. Login with admin credentials
2. Change all default passwords
3. Add real buses and schedules
4. Create operator accounts
5. Test passenger booking flow
6. Configure email for production

**Support:** Check SETUP.md for detailed troubleshooting and configuration guides.
