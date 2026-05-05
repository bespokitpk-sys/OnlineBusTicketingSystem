# 🔐 Professional Role-Based Authentication Setup
## Complete Setup Instructions

---

## **STEP 1: Create Permanent Credentials in Database** (1 minute)

### Visit this URL:
```
http://localhost/BusTicketingSystem/setup_permanent_credentials.php
```

### What it does:
✅ Creates **Admin** account (if not exists):
- Email: `admin123@gmail.com`
- Password: `Password@123`
- Role: `admin`
- Verified: Yes ✓

✅ Creates **Operator** account (if not exists):
- Email: `operator123@gmail.com`
- Password: `Password1@23`
- Role: `operator`
- Verified: Yes ✓

### You'll see:
```
✅ Admin account created successfully!
✅ Operator account created successfully!
```

---

## **STEP 2: Home Page Now Shows Role-Based Logins** ✅

### Visit:
```
http://localhost/BusTicketingSystem/
```

### You'll see in Navbar:
```
🚌 Book Smarter, Travel Better
[Admin Login] [Operator Login] [Passenger Login] [Search]
```

---

## **STEP 3: Login as Admin**

### Click: **"Admin Login"** button

### You'll be redirected to:
```
http://localhost/BusTicketingSystem/admin/login.php
```

### Enter credentials:
```
Email: admin123@gmail.com
Password: Password@123
```

### Click: **"Login as Admin"**

### You'll be redirected to:
```
http://localhost/BusTicketingSystem/admin/dashboard.php
✅ Admin Dashboard (Create operators, manage buses, etc.)
```

---

## **STEP 4: Login as Operator**

### Click: **"Operator Login"** button (from home page)

### You'll be redirected to:
```
http://localhost/BusTicketingSystem/public/operator_login.php
```

### Enter credentials:
```
Email: operator123@gmail.com
Password: Password1@23
```

### Click: **"Login as Operator"**

### You'll be redirected to:
```
http://localhost/BusTicketingSystem/operator/dashboard.php
✅ Operator Dashboard (Manage trips, approve payments, board passengers)
```

---

## **STEP 5: Passenger Registration (Optional)**

### Click: **"Passenger Login"** button

### You'll be redirected to:
```
http://localhost/BusTicketingSystem/public/auth_router.php?action=login
```

### Or register a new passenger:
```
http://localhost/BusTicketingSystem/public/auth_router.php?action=register
```

### Passenger workflow:
1. Register with email/phone/password
2. Receive OTP
3. Verify OTP
4. Login as passenger
5. Access passenger dashboard

---

## **🔒 Security Features Implemented**

### ✅ Role-Based Login Enforcement
- Admin login ONLY accepts admin role
- Operator login ONLY accepts operator role
- Passenger login for regular users

### ✅ Permanent Credentials
- Admin and Operator accounts pre-created
- Cannot be modified through registration
- Protected with strong password hashing

### ✅ Verification Status
- Admin: is_verified = 1 (can login immediately)
- Operator: is_verified = 1 (can login immediately)
- Passenger: is_verified = 0 (needs OTP verification)

### ✅ Error Handling
- Invalid email/password → "Invalid email or password"
- Wrong role → "Invalid email or password"
- Account not verified → "Account not verified"

---

## **📋 Complete Credentials Reference**

### Admin Account (Permanent)
```
Email: admin123@gmail.com
Password: Password@123
Role: Admin
Login: /admin/login.php
Dashboard: /admin/dashboard.php
```

### Operator Account (Permanent)
```
Email: operator123@gmail.com
Password: Password1@23
Role: Operator
Login: /public/operator_login.php
Dashboard: /operator/dashboard.php
```

### Passenger Account (Registered)
```
Email: Any email (you register)
Password: Any password (you set)
Role: Passenger
Login: /public/auth_router.php?action=login
Dashboard: /passenger/dashboard.php
```

---

## **🧪 Complete Test Flow**

### Test 1: Admin Login
1. Go to home page
2. Click "Admin Login"
3. Enter: admin123@gmail.com / Password@123
4. ✅ See Admin Dashboard

### Test 2: Operator Login
1. Go to home page
2. Click "Operator Login"
3. Enter: operator123@gmail.com / Password1@23
4. ✅ See Operator Dashboard

### Test 3: Passenger Registration & Login
1. Go to home page
2. Click "Passenger Login"
3. Click "Register"
4. Fill form with your details
5. Verify OTP
6. ✅ See Passenger Dashboard

### Test 4: Invalid Credentials
1. Try admin login with wrong password
2. ✅ See error: "Invalid email or password"

### Test 5: Wrong Role
1. Try to login to Admin panel with operator email
2. ✅ See error: "Invalid email or password"

---

## **📁 File Locations**

```
Admin Login Page:
/admin/login.php

Operator Login Page:
/public/operator_login.php

Passenger Login Page:
/public/auth_router.php?action=login

Setup Script:
/setup_permanent_credentials.php

Home Page (with login buttons):
/index.php
```

---

## **🔄 User Flow Diagram**

```
Home Page (index.php)
│
├─→ [Admin Login] ───→ /admin/login.php ───→ Admin Dashboard
│
├─→ [Operator Login] ──→ /public/operator_login.php ───→ Operator Dashboard
│
└─→ [Passenger Login] ──→ /public/auth_router.php ───→ Passenger Dashboard
```

---

## **✅ Verification Checklist**

- [ ] Ran setup_permanent_credentials.php
- [ ] Admin and Operator accounts created in database
- [ ] Home page shows 3 login buttons
- [ ] Admin login works with admin123@gmail.com / Password@123
- [ ] Operator login works with operator123@gmail.com / Password1@23
- [ ] Each login redirects to correct dashboard
- [ ] Invalid credentials show error message
- [ ] Wrong role shows error message

---

## **🎯 Professional Highlights**

✅ **Role-Based Separation**: Each role has dedicated login page
✅ **Permanent Credentials**: Admin/Operator accounts pre-created
✅ **Professional UI**: Beautiful, consistent design across all pages
✅ **Security**: Password hashing, role validation, error handling
✅ **User Experience**: Clear navigation, helpful error messages
✅ **Scalability**: Easy to add more roles or accounts

---

## **📞 Troubleshooting**

### Problem: "Setup page shows nothing"
**Solution**: 
- Check database connection is working
- Run setup_permanent_credentials.php again
- Refresh browser (Ctrl+F5)

### Problem: "Login not working"
**Solution**:
- Verify credentials are correct (check database)
- Make sure is_verified = 1
- Check role matches (admin vs operator)

### Problem: "Wrong role error"
**Solution**:
- Use correct login page for your role
- Admin → /admin/login.php
- Operator → /public/operator_login.php

---

## **🚀 Next Steps After Setup**

1. ✅ Login as Admin
2. ✅ Create test operators (if needed)
3. ✅ Create test schedules/buses
4. ✅ Login as Operator and manage trips
5. ✅ Create test passengers
6. ✅ Book tickets as passenger

---

**Status**: ✅ Professional Role-Based Authentication System Ready!
**Implementation**: Complete with permanent credentials
**Security**: ✅ Role-based, password-hashed, error-handled
