# Bus Ticketing System - Setup Guide

## 📋 Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB
- Apache/Nginx web server
- Composer (optional)

---

## 🚀 Local Development Setup (Laragon/XAMPP/WAMP)

### Step 1: Database Setup
1. Start MySQL server (Laragon/XAMPP)
2. Open phpMyAdmin or MySQL command line
3. Create database:
   ```sql
   CREATE DATABASE bus_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Import the database schema:
   ```sql
   USE bus_db;
   SOURCE database.sql;
   ```
   Or import `database.sql` via phpMyAdmin

### Step 2: Configure Database Connection
Edit `config/db.php`:
```php
$dbHost = 'localhost';
$dbUser = 'root';           // Your MySQL username
$dbPass = '';               // Your MySQL password (blank for local)
$dbName = 'bus_db';         // Database name
```

### Step 3: Create Default Admin Account
Run `setup.php` in your browser:
```
http://localhost/BusTicketingSystem/setup.php
```
This will create a default admin account.

### Step 4: Access the Application
```
http://localhost/BusTicketingSystem/public/
```

**Default Admin Credentials (after running setup.php):**
- Email: `admin@busticketing.com`
- Password: `Admin@123`

⚠️ **Change these credentials immediately after first login!**

---

## 🌐 cPanel Deployment Setup

### Step 1: Upload Files
1. Compress your project folder (excluding unnecessary files)
2. Upload via cPanel File Manager or FTP to `public_html/`
3. Extract the files

**Recommended Structure:**
```
public_html/
├── public/          (set as document root)
├── app/
├── config/
├── uploads/
├── database.sql
├── setup.php
└── .htaccess
```

### Step 2: Configure Document Root
1. In cPanel, go to **Domains** or **Advanced DNS Zone Editor**
2. Set document root to `public_html/public/` (not the main public_html)
3. Or use `.htaccess` redirect (see below)

### Step 3: Create MySQL Database
1. Go to **cPanel → MySQL Databases**
2. Create a new database: `cpanel_username_bus_db`
3. Create a MySQL user: `cpanel_username_bususer`
4. Set a strong password
5. Add user to database with ALL PRIVILEGES
6. Note down credentials:
   - Host: `localhost`
   - Database: `cpanelusername_bus_db`
   - Username: `cpanelusername_bususer`
   - Password: `your_password`

### Step 4: Import Database
1. Go to **cPanel → phpMyAdmin**
2. Select your database
3. Click **Import** tab
4. Upload `database.sql`
5. Click **Go**

### Step 5: Update Database Configuration
Edit `config/db.php` via cPanel File Manager:
```php
$dbHost = 'localhost';
$dbUser = 'cpanelusername_bususer';     // Your cPanel MySQL username
$dbPass = 'your_strong_password';        // Your cPanel MySQL password
$dbName = 'cpanelusername_bus_db';      // Your cPanel database name
```

### Step 6: Run Setup Script
Visit in your browser:
```
https://yourdomain.com/setup.php
```
This creates the default admin account.

### Step 7: Set Permissions
Via cPanel File Manager or SSH:
```bash
chmod 755 public/
chmod 755 uploads/
chmod 755 uploads/profiles/
chmod 644 config/db.php
```

### Step 8: Delete Setup File (Important!)
After successful setup, **delete** `setup.php` for security:
```bash
rm setup.php
```

### Step 9: Access Your Application
```
https://yourdomain.com/
```

---

## 🔐 Default Credentials Created by setup.php

### Admin Account
- **Email**: `admin@busticketing.com`
- **Password**: `Admin@123`
- **Role**: admin
- **Status**: Verified

### Test Operator Account
- **Email**: `operator@busticketing.com`
- **Password**: `Operator@123`
- **Role**: operator
- **Status**: Verified

### Passenger Accounts
- **Note**: Passengers register themselves via the registration page
- No default passenger account is created for security reasons

⚠️ **SECURITY WARNING**: Change all default passwords immediately after first login!

---

## 📁 File Structure
```
BusTicketingSystem/
├── public/                 # Document root (public files)
│   ├── index.php          # Front controller
│   └── assets/            # CSS, JS, images
├── app/
│   ├── controllers/       # Application controllers
│   ├── models/           # Database models
│   ├── views/            # View templates
│   └── core/             # Core files (Auth)
├── config/
│   └── db.php            # Database configuration
├── uploads/
│   └── profiles/         # User profile pictures
├── database.sql          # Database schema
├── setup.php             # Setup script (delete after use)
└── .htaccess            # Apache configuration
```

---

## 🔧 Troubleshooting

### Issue: 404 Not Found
**Solution**: Ensure `.htaccess` is working and `mod_rewrite` is enabled
```apache
# In public/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]
```

### Issue: Database Connection Failed
**Solution**: 
1. Verify credentials in `config/db.php`
2. Check if MySQL is running
3. Verify user has proper privileges

### Issue: Can't Login / Credentials Not Working
**Solution**:
1. Run `setup.php` to create default accounts
2. Use default credentials listed above
3. Check `users` table exists: `SELECT * FROM users;`
4. Reset password manually if needed (see SQL below)

### Issue: OTP Not Showing
**Solution**: OTPs are stored in session for testing. Check:
```php
echo $_SESSION['test_otp']; // The OTP code
```

### Issue: Upload Directory Not Writable
**Solution**:
```bash
chmod 755 uploads/
chmod 755 uploads/profiles/
```

---

## 🔑 Manual Password Reset (If Needed)

If you forget admin password, run this in phpMyAdmin:

```sql
-- Generate password hash for 'Admin@123'
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@busticketing.com';
```

Or create new admin:
```sql
INSERT INTO users (name, email, phone, password_hash, role, is_verified, created_at) 
VALUES (
    'System Admin',
    'admin@example.com',
    '1234567890',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW()
);
```
Password for above hash: `Admin@123`

---

## 🚨 Security Checklist

- [ ] Change all default passwords
- [ ] Delete `setup.php` after installation
- [ ] Delete `diagnose.php` and `clear_cache.php` in production
- [ ] Update database credentials in `config/db.php`
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Enable HTTPS on production
- [ ] Disable error display in production (`display_errors = Off`)
- [ ] Regular database backups
- [ ] Keep PHP and MySQL updated

---

## 📞 Support

If you encounter issues:
1. Check error logs (cPanel → Error Logs)
2. Enable PHP error reporting temporarily
3. Verify all setup steps completed
4. Check file permissions
5. Verify database connection

---

## 📝 Post-Installation Tasks

1. **Change Default Passwords**: Login and update all default account passwords
2. **Configure Email**: Update `AuthController.php` to use real email service (SMTP)
3. **Add Buses**: Go to Admin Dashboard → Manage Buses
4. **Create Schedules**: Add bus routes and schedules
5. **Test Booking**: Create test booking as passenger
6. **Configure Operators**: Assign operators to routes
7. **Customize**: Update branding, colors, and content as needed

---

## 🎉 Installation Complete!

Your Bus Ticketing System is now ready to use. Access the application and login with admin credentials to get started.

**Important**: Remember to delete `setup.php` after successful installation!
