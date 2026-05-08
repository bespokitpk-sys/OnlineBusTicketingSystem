# Quick Reference - Bus Ticketing System

## 🔑 Default Login Credentials (After Running setup.php)

### Administrator
- **Email:** admin@busticketing.com
- **Password:** Admin@123
- **Access:** Full system control

### Operator  
- **Email:** operator@busticketing.com
- **Password:** Operator@123
- **Access:** Manage schedules, boarding, payments

### Passengers
- **Note:** Passengers register themselves via registration page
- No default passenger credentials (self-registration only)

⚠️ **IMPORTANT:** Change these passwords immediately after first login!

---

## 💾 Database Configuration

### Local Development (Laragon/XAMPP)
```php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'bus_db';
```

### cPanel Production
```php
$dbHost = 'localhost';
$dbUser = 'cpanelusername_bus_user';
$dbPass = 'your_password_here';
$dbName = 'cpanelusername_bus_ticketing';
```

---

## 🚀 Installation Steps (Super Quick)

### Local Setup
1. Import `database.sql` in phpMyAdmin
2. Update `config/db.php` with database credentials
3. Visit `http://localhost/BusTicketingSystem/setup.php`
4. Login with admin credentials
5. Delete `setup.php`

### cPanel Deployment
1. Create MySQL database in cPanel
2. Import `database.sql` via phpMyAdmin
3. Upload project files to `public_html/`
4. Update `config/db.php` with cPanel database credentials
5. Set document root to `public/` folder
6. Visit `https://yourdomain.com/setup.php`
7. Delete `setup.php` after completion

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `setup.php` | Creates default accounts (DELETE after use) |
| `database.sql` | Database schema with sample data |
| `config/db.php` | Database configuration |
| `SETUP.md` | Detailed setup instructions |
| `CPANEL_DEPLOYMENT.md` | cPanel deployment guide |
| `.htaccess` | Apache configuration |

---

## 🔧 Common Commands

### Create Admin Account Manually (MySQL)
```sql
INSERT INTO users (name, email, phone, password_hash, role, is_verified, created_at) 
VALUES (
    'Admin User',
    'admin@example.com',
    '1234567890',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW()
);
```
Password for hash above: `Admin@123`

### Reset User Password
```sql
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@busticketing.com';
```

### Check if Tables Exist
```sql
SHOW TABLES;
```

### View All Users
```sql
SELECT id, name, email, role, is_verified FROM users;
```

---

## 🌐 Access URLs

### Local Development
- Homepage: `http://localhost/BusTicketingSystem/`
- Login: `http://localhost/BusTicketingSystem/login`
- Setup: `http://localhost/BusTicketingSystem/setup.php`

### Production (cPanel)
- Homepage: `https://yourdomain.com/`
- Login: `https://yourdomain.com/login`
- Admin Dashboard: `https://yourdomain.com/admin/dashboard`
- Operator Dashboard: `https://yourdomain.com/operator/dashboard`
- Passenger Dashboard: `https://yourdomain.com/passenger/dashboard`

---

## ⚡ Troubleshooting Quick Fixes

### Can't Login?
1. Run `setup.php` to create accounts
2. Check database has users: `SELECT * FROM users;`
3. Verify `is_verified = 1` for the user

### Database Connection Failed?
1. Check credentials in `config/db.php`
2. Verify MySQL is running
3. Test in phpMyAdmin

### 404 Errors on Pages?
1. Check `.htaccess` exists in `public/` folder
2. Enable `mod_rewrite` in Apache
3. Verify document root points to `public/`

### OTP Not Working?
OTPs are stored in session for testing:
```php
// Check session
echo $_SESSION['test_otp'];
```

### File Upload Fails?
```bash
chmod 755 uploads/
chmod 755 uploads/profiles/
```

---

## 🔐 Security Reminders

1. ✅ Delete `setup.php` after installation
2. ✅ Delete `diagnose.php` in production
3. ✅ Delete `clear_cache.php` in production
4. ✅ Change all default passwords
5. ✅ Enable HTTPS in production
6. ✅ Set proper file permissions (755/644)
7. ✅ Configure real email service (not session-based)
8. ✅ Regular database backups

---

## 📊 File Permissions

| Path | Permission | Purpose |
|------|------------|---------|
| `uploads/` | 755 | Allow file uploads |
| `uploads/profiles/` | 755 | Allow profile pictures |
| `config/db.php` | 644 | Protect database config |
| `*.php` files | 644 | Standard PHP files |
| Directories | 755 | Allow directory access |

---

## 🎯 Post-Installation Tasks

1. **Login as Admin** → `admin@busticketing.com` / `Admin@123`
2. **Change Password** → Go to profile settings
3. **Add Buses** → Admin Dashboard → Manage Buses → Add New
4. **Create Schedules** → Admin Dashboard → Manage Schedules
5. **Add Operators** → Admin Dashboard → Manage Users → Add Operator
6. **Assign Routes** → Link operators to schedules
7. **Test Booking** → Login as passenger and book a ticket
8. **Configure Email** → Update AuthController.php with SMTP

---

## 📞 Support Files

- **Full Setup Guide:** `SETUP.md`
- **cPanel Guide:** `CPANEL_DEPLOYMENT.md`  
- **This File:** `QUICKREF.md`

---

## 💡 Tips

- **Local Testing:** Use `http://localhost/BusTicketingSystem/public/`
- **OTP Display:** Check session variable `$_SESSION['test_otp']`
- **Manual SQL:** Use phpMyAdmin for direct database access
- **Logs:** Check PHP error logs in cPanel
- **Fresh Start:** Drop all tables and re-import `database.sql`

---

**Last Updated:** May 2026  
**Version:** 1.0
