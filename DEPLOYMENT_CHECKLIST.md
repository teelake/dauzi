# Deployment Checklist - Contact Form

## ✅ Pre-Deployment (Completed)

### 1. PHPMailer Installed ✓
- PHPMailer v7.0.0 installed via Composer
- Location: `vendor/phpmailer/phpmailer/`
- Autoloader: `vendor/autoload.php`

### 2. Security Implemented ✓
- Credentials moved to `config.php` (protected by .htaccess)
- POST-redirect-GET pattern implemented
- Duplicate submission prevention (server-side + client-side)
- Session-based protection
- Form submission hash checking (30-second cooldown)

### 3. Database Ready ✓
- Schema created: `database-schema.sql`
- Credentials configured in `config.php`
- Table: `contact_submissions`

## 📦 Files to Upload to cPanel

### Required Files:
```
✅ vendor/                    (PHPMailer - entire folder)
✅ contact.html               (Updated form)
✅ contact-handler-smtp.php   (Main handler)
✅ contact-handler.php        (Fallback handler)
✅ config.php                 (Secure credentials)
✅ .htaccess                  (Security protection)
✅ database-schema.sql        (For reference)
```

### Optional Files:
```
📄 INSTALLATION.md
📄 DEPLOYMENT_CHECKLIST.md
📄 SMTP_SETUP.md
```

## 🔧 Post-Deployment Steps

### Step 1: Verify PHPMailer
- Check that `vendor/autoload.php` exists
- Verify `vendor/phpmailer/phpmailer/src/` contains PHPMailer files

### Step 2: Create Database Table
1. Open phpMyAdmin
2. Select database: `dauzicon_db`
3. Go to SQL tab
4. Run:
```sql
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 3: Verify Config File
- Check `config.php` has correct credentials
- Verify `.htaccess` is protecting `config.php`
- Test: Try accessing `yoursite.com/config.php` (should be blocked)

### Step 4: Test Form
1. Fill out contact form
2. Submit
3. Check:
   - ✓ Success message appears
   - ✓ Form clears automatically
   - ✓ No duplicate on refresh
   - ✓ Email received at both addresses
   - ✓ Database entry created (if enabled)

### Step 5: Test Duplicate Prevention
1. Submit form
2. Immediately refresh page (F5)
3. Should NOT send duplicate email
4. Should show same success message

## 🛡️ Security Features Implemented

1. **POST-redirect-GET Pattern**
   - Prevents form resubmission on refresh
   - Uses HTTP redirect after POST

2. **Session-based Protection**
   - Tracks submission hash
   - 30-second cooldown for same content

3. **Client-side Protection**
   - Form disables after submission
   - URL parameters cleared after reading
   - Double-submit prevention

4. **Server-side Validation**
   - Input sanitization
   - Required field validation
   - Email format validation

5. **Secure Credentials**
   - Config file protected by .htaccess
   - No hardcoded passwords in code

## 🐛 Troubleshooting

### Emails Not Sending?
- Check SMTP credentials in `config.php`
- Verify PHPMailer is installed correctly
- Try changing SMTP_PORT to 465 and SMTP_SECURE to 'ssl'
- Enable debug: Uncomment `$mail->SMTPDebug = 2;` in handler

### Duplicate Submissions?
- Check session is working (look for session files in server)
- Verify JavaScript is enabled
- Check browser console for errors

### Database Not Saving?
- Verify table exists
- Check database credentials in `config.php`
- Verify user has INSERT permissions
- Check `ENABLE_DATABASE` is `true` in config.php

## ✨ Features Summary

✅ SMTP authentication with PHPMailer
✅ Secure credential storage
✅ Database backup of submissions
✅ Dual email delivery (info@ + Gmail)
✅ POST-redirect-GET pattern
✅ Duplicate submission prevention
✅ Form auto-clear on success
✅ Loading state during submission
✅ Success/error message display
✅ Subject field mandatory

## 🚀 Ready to Deploy!

All files are ready. Upload to cPanel and test!

