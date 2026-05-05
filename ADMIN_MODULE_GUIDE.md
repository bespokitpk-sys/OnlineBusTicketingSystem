# 🎯 Admin Dashboard Module - Complete Guide

## Overview
The Admin Dashboard is a professional, role-based administration system following the MVC (Model-View-Controller) architecture. It provides complete control over buses, operators, users, schedules, tickets, and system analytics.

## Architecture

### MVC Pattern
```
Models/              AdminController (Logic Layer)
   ↓                        ↓
Views/               admin/dashboard.php
   ↓                manage_buses.php
Controllers/         manage_users.php
                     manage_tickets.php
                     manage_schedules.php
                     reports.php
```

---

## Module Components

### 1. **Admin Dashboard** (`admin/dashboard.php`)
**Purpose:** Main hub for admin operations with statistics and quick navigation

**Features:**
- 📊 Real-time statistics (buses, operators, passengers, schedules, tickets, active bookings)
- 🎯 Quick action cards for each module
- 📈 System overview with key metrics
- Professional, responsive design

**Statistics Shown:**
- Total Buses: Count of all buses in system
- Total Operators: Count of all operator accounts
- Total Passengers: Count of all passenger accounts
- Total Schedules: Count of all trip schedules
- Total Tickets: Count of all tickets (booked + cancelled)
- Active Bookings: Confirmed and ongoing bookings

---

### 2. **Bus Management** (`admin/add_bus.php`, `admin/manage_buses.php`)

#### Methods in AdminController:
```php
addBus(name, type, capacity, operator_id)
getAllBuses()
getBusById(id)
updateBus(id, name, type, capacity)
deleteBus(id)
```

**Features:**
- ✅ Add new buses with capacity
- ✅ Assign buses to operators
- ✅ Update bus information
- ✅ Delete buses from system
- ✅ View all buses with operator details

---

### 3. **Operator Management** (`admin/add_operator.php`, Admin uses manage_users.php)

#### Methods in AdminController:
```php
createOperator(name, email, phone, password)
getOperators()
getOperatorById(id)
updateOperator(id, name, phone)
deleteOperator(id)
```

**Features:**
- ✅ Create new operators without registration process
- ✅ Auto-verify operator accounts
- ✅ Update operator details
- ✅ View operator statistics (buses managed, schedules, etc.)
- ✅ Delete operator accounts
- ✅ Pre-set passwords for operators

**Validation:**
- Email must be unique and valid
- Phone number required
- Password minimum 6 characters
- Name is required

---

### 4. **User Management** (`admin/manage_users.php`)

#### Methods in AdminController:
```php
getAllUsers()
getUsersByRole(role)
verifyUser(id)
deleteUser(id)
```

**Features:**
- 👥 View all users (admin, operator, passenger)
- ✅ Filter by role
- ✅ Verify unverified accounts
- ✅ Delete user accounts
- ✅ View user details (email, phone, created date)
- 🔒 Cannot delete admin accounts

---

### 5. **Ticket Management** (`admin/manage_tickets.php`)

#### Methods used:
```php
// Get all tickets via SQL query
// Filter by status (booked, confirmed, completed, cancelled)
// Cancel tickets
```

**Features:**
- 🎫 View all tickets with passenger info
- 🔍 Filter by status
- ✅ Cancel tickets
- 💰 View ticket price
- 📅 Track booking dates
- 🚐 See bus information
- ⏰ Departure/arrival times

---

### 6. **Schedule Management** (`admin/manage_schedules.php`)

**Features:**
- 📅 View all schedules created by operators
- 🚐 See associated bus and operator
- ⏰ Check departure and arrival times
- 💺 Monitor seat capacity and availability
- 📊 Track schedule creation date

---

### 7. **Reports & Analytics** (`admin/reports.php`)

#### Methods in AdminController:
```php
getRevenueReport()
getCancelledTickets()
getDashboardStats()
```

**Features:**
- 💰 Revenue reports (last 30 days)
- 📊 Ticket statistics (booked, confirmed, cancelled, completed)
- ❌ Cancelled tickets tracking
- 📈 Daily revenue breakdown
- 🎯 System-wide analytics

---

## Controller Methods Reference

### Dashboard Statistics
```php
$stats = AdminController::getDashboardStats();
// Returns: [
//   'total_buses' => int,
//   'total_operators' => int,
//   'total_passengers' => int,
//   'total_schedules' => int,
//   'total_tickets' => int,
//   'active_bookings' => int
// ]
```

### Bus Management
```php
// Add bus
$result = AdminController::addBus('AC Bus', 'Luxury', 50, 1);
// Returns: ['success' => bool, 'message' => string, 'bus_id' => int]

// Get all buses
$buses = AdminController::getAllBuses();
// Returns: Array of buses with operator details

// Update bus
$result = AdminController::updateBus(1, 'Updated Name', 'Deluxe', 48);
```

### Operator Management
```php
// Create operator
$result = AdminController::createOperator(
    'Operator Name',
    'operator@email.com',
    '03001234567',
    'password123'
);

// Get operators
$operators = AdminController::getOperators();
// Returns: Array with operator count and bus count
```

### Reports
```php
// Revenue report
$revenue = AdminController::getRevenueReport();
// Returns: Array of daily revenue data (last 30 days)

// Cancelled tickets
$cancelled = AdminController::getCancelledTickets();
```

---

## Security Features

### 1. **Role-Based Access Control**
```php
requireRole('admin'); // Only admin can access
```

### 2. **SQL Injection Prevention**
```php
$name = $conn->real_escape_string($name);
```

### 3. **Account Protection**
- Admins cannot be deleted
- Pre-verified operator accounts
- Session-based authentication

### 4. **Data Validation**
- Email format validation
- Password strength requirements
- Input sanitization

---

## Database Tables Used

### users
- id, name, email, phone, password_hash, role, is_verified, created_at

### buses
- id, name, bus_type, capacity, operator_id, created_at

### schedules
- id, bus_id, departure_time, arrival_time, available_seats, created_at

### tickets
- id, user_id, schedule_id, seat_number, price, status, created_at

---

## Navigation Flowchart

```
Dashboard (admin/dashboard.php)
├─ Bus Management
│  ├─ Add Bus (admin/add_bus.php)
│  └─ View All (admin/manage_buses.php)
├─ Operator Management
│  ├─ Add Operator (admin/add_operator.php)
│  └─ View All (admin/manage_users.php?role=operator)
├─ User Management (admin/manage_users.php)
├─ Ticket Management (admin/manage_tickets.php)
├─ Schedule Management (admin/manage_schedules.php)
└─ Reports (admin/reports.php)
```

---

## Module Statistics

| Component | Lines of Code | Status |
|-----------|---------------|--------|
| AdminController.php | 400+ | ✅ Complete |
| admin/dashboard.php | 300+ | ✅ Complete |
| admin/manage_tickets.php | 200+ | ✅ Complete |
| admin/manage_schedules.php | 180+ | ✅ Complete |
| admin/reports.php | 250+ | ✅ Complete |
| **Total** | **1330+** | **✅ Complete** |

---

## User Credentials

### Admin Access
```
Email: admin123@gmail.com
Password: Password@123
```

### Operator Access
```
Email: operator123@gmail.com
Password: Password1@23
```

---

## Testing Checklist

- [ ] Login with admin credentials
- [ ] Dashboard loads statistics correctly
- [ ] Add new bus
- [ ] Create new operator
- [ ] View all users
- [ ] Filter tickets by status
- [ ] View revenue reports
- [ ] Check cancelled tickets
- [ ] Test responsive design
- [ ] Verify role-based access

---

## Module 4 Ready ✅

All admin functionality is implemented following professional MVC pattern. Ready to proceed to Passenger Module.
