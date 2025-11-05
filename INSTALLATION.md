# Installation Guide - Contact Form Setup

## ✅ What's Been Configured

1. **Form switched to PHPMailer** (contact-handler-smtp.php)
2. **Secure config file created** (config.php) with all credentials
3. **Database connection configured** with your credentials
4. **.htaccess protection** added for config.php

## 📦 Step 1: Install PHPMailer

### Option A: Via Composer (Recommended)
```bash
composer require phpmailer/phpmailer
```

### Option B: Manual Download
1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer/releases
2. Extract the ZIP file
3. Copy the `src` folder contents to `vendor/PHPMailer/PHPMailer/` in your project
4. Create `vendor/autoload.php` with:
```php
<?php
require_once __DIR__ . '/PHPMailer/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/PHPMailer/src/SMTP.php';
```

## 🗄️ Step 2: Create Database Table

1. Open phpMyAdmin in cPanel
2. Select database: `dauzicon_db`
3. Go to SQL tab
4. Run this command:
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

Or import the `database-schema.sql` file directly.

## 🔒 Step 3: Verify Security

- ✅ `config.php` is protected by `.htaccess` (blocks direct access)
- ✅ Database credentials stored securely
- ✅ SMTP password stored securely

## 🧪 Step 4: Test the Form

1. Upload all files to cPanel
2. Test the contact form
3. Check both email inboxes:
   - info@dauziconsulting.com
   - dauziconsulting@gmail.com
4. Check database in phpMyAdmin to verify submissions are saved

## 📁 Files to Upload

- ✅ contact.html (updated)
- ✅ contact-handler-smtp.php (main handler)
- ✅ contact-handler.php (fallback)
- ✅ config.php (secure credentials)
- ✅ .htaccess (security)
- ✅ database-schema.sql (optional, for reference)
- ✅ vendor/ folder (PHPMailer - if manual install)

## ⚙️ Configuration

All settings are in `config.php`:
- Database credentials ✓
- SMTP credentials ✓
- Email addresses ✓

To disable database storage, set in `config.php`:
```php
define('ENABLE_DATABASE', false);
```

## 🐛 Troubleshooting

**Emails not sending?**
- Check PHPMailer is installed correctly
- Verify SMTP credentials in config.php
- Try changing SMTP_PORT to 465 and SMTP_SECURE to 'ssl'
- Check cPanel email account exists

**Database not saving?**
- Verify table exists (run SQL from Step 2)
- Check database credentials in config.php
- Verify user has INSERT permissions

## ✨ Ready to Go!

Your contact form is now configured with:
- ✅ SMTP authentication
- ✅ Secure credential storage
- ✅ Database backup
- ✅ Dual email delivery
- ✅ Subject field mandatory

