# Registration with CNIC and Profile Picture - Setup Instructions

## Overview
The registration system has been enhanced with two new fields:
1. **CNIC (Computerized National Identity Card)** - Required field
2. **Profile Picture** - Optional file upload

---

## Setup Steps

### 1. Run Database Migration
Before using the new registration features, you need to add the new columns to the users table.

**Open in browser:**
```
http://ticketingsystem.test/add_user_profile_fields.php
```

Or if using localhost:
```
http://localhost/BusTicketingSystem/add_user_profile_fields.php
```

This will add:
- `cnic` VARCHAR(15) column
- `profile_picture` VARCHAR(255) column

### 2. Verify Directory Structure
The following directories have been created:
```
uploads/
└── profiles/      (stores user profile pictures)
```

### 3. Set Directory Permissions (if needed)
On Linux/Mac:
```bash
chmod 755 uploads
chmod 755 uploads/profiles
```

On Windows (Laragon):
- Directory permissions are usually handled automatically
- If you encounter upload errors, check that the web server has write access to the uploads folder

---

## Features

### CNIC Field
- **Format:** 13 digits without dashes
- **Validation:** Client-side and server-side validation
- **Required:** Yes
- **Example:** 1234567890123

### Profile Picture Upload
- **File Types:** JPG, JPEG, PNG, GIF
- **Max Size:** 2MB
- **Required:** No (optional)
- **Storage:** `/uploads/profiles/`
- **Filename:** `profile_[timestamp]_[unique_id].[extension]`

### Security Features
- File type validation (only images allowed)
- File size validation (max 2MB)
- Unique filename generation to prevent overwrites
- `.htaccess` file to prevent script execution in uploads folder
- Server-side validation for all inputs

---

## Files Modified

### Backend
1. **controllers/AuthController.php**
   - Added CNIC validation
   - Added file upload handling
   - Updated User::create() call

2. **models/User.php**
   - Updated create() method to accept cnic and profile_picture parameters
   - Modified database insert query

### Frontend
3. **views/register_form.php**
   - Added CNIC input field
   - Added profile picture file input
   - Added `enctype="multipart/form-data"` to form

4. **assets/css/style.css**
   - Added styling for `input[type="file"]`
   - Dashed border for file input
   - Hover and focus states

### Database
5. **add_user_profile_fields.php**
   - Migration script to add new columns

### Security
6. **uploads/.htaccess**
   - Prevents PHP execution in uploads directory

---

## Testing

### Test Registration
1. Go to registration page
2. Fill in all required fields:
   - Full Name
   - Email
   - Phone Number
   - **CNIC (13 digits)**
   - Password & Confirm Password
   - **Profile Picture (optional)**
   - Agree to terms
3. Submit form
4. Verify OTP
5. Check user dashboard

### Verify Database
```sql
SELECT id, name, email, cnic, profile_picture FROM users ORDER BY id DESC LIMIT 5;
```

### Check Uploaded Files
Navigate to: `uploads/profiles/`
- Files should have names like: `profile_1746473829_6543abc123.jpg`

---

## Database Schema Update

```sql
ALTER TABLE users ADD COLUMN cnic VARCHAR(15) NULL AFTER phone;
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL AFTER cnic;
```

---

## Troubleshooting

### CNIC Validation Error
- Ensure CNIC is exactly 13 digits
- No dashes, spaces, or letters allowed

### File Upload Fails
- Check file size (must be under 2MB)
- Check file type (must be JPG, PNG, or GIF)
- Verify uploads/profiles/ directory exists
- Check directory write permissions

### Column Already Exists Error
- This means the migration was already run
- No action needed, columns already exist

---

## Next Steps (Optional Enhancements)

1. **Display Profile Picture**
   - Show profile picture in user dashboards
   - Add to passenger/operator profiles

2. **Edit Profile**
   - Allow users to update CNIC
   - Allow users to change profile picture

3. **CNIC Validation**
   - Add advanced CNIC validation (check digit verification)
   - Verify CNIC uniqueness

4. **Image Optimization**
   - Resize uploaded images
   - Compress images to save storage

---

## Security Recommendations

✅ **Implemented:**
- File type validation
- File size limits
- Unique filenames
- Script execution prevention in uploads folder

📋 **Consider Adding:**
- Image dimension validation
- MIME type checking (not just extension)
- Virus scanning for uploaded files
- Rate limiting on uploads

---

## Support

If you encounter any issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs
3. Verify database columns were created
4. Ensure uploads directory has write permissions
