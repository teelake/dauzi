# SMTP Email Configuration Guide

## Current Setup

Your contact form is configured to use:
- **From Email:** no-reply@dauziconsulting.com
- **Password:** Temp_Pass123 (configured in code)
- **Recipients:** 
  - info@dauziconsulting.com (primary)
  - dauziconsulting@gmail.com (copy)

## Two Methods Available

### Method 1: Basic PHP mail() (Currently Active)
- File: `contact-handler.php`
- Uses PHP's built-in mail() function
- Works on most cPanel servers
- May require server configuration

### Method 2: PHPMailer with SMTP (More Reliable)
- File: `contact-handler-smtp.php` (provided)
- Requires PHPMailer library
- Better deliverability
- More reliable on cPanel

## Recommended: Use PHPMailer

### Step 1: Install PHPMailer

**Option A: Via Composer (if available)**
```bash
composer require phpmailer/phpmailer
```

**Option B: Manual Download**
1. Download from: https://github.com/PHPMailer/PHPMailer
2. Extract to `vendor/` folder in your project
3. Update the require path in `contact-handler-smtp.php` if needed

### Step 2: Update Contact Form

Change `contact.html` form action from:
```html
action="contact-handler.php"
```

To:
```html
action="contact-handler-smtp.php"
```

### Step 3: Configure SMTP Settings

In `contact-handler-smtp.php`, adjust these if needed:
- `$mail->Host = 'mail.dauziconsulting.com';` (or 'localhost' for cPanel)
- `$mail->Port = 587;` (or 465 for SSL)
- `$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;` (or SSL)

**Common cPanel SMTP Settings:**
- Host: `mail.dauziconsulting.com` or `localhost`
- Port: `587` (TLS) or `465` (SSL)
- Encryption: STARTTLS (587) or SSL (465)

## Testing

1. Upload files to your server
2. Submit the contact form
3. Check both email inboxes
4. Check spam folders if needed

## Troubleshooting

**Emails not sending?**
- Check SMTP credentials are correct
- Verify port 587 or 465 is open
- Check cPanel email account exists and password is correct
- Try switching between STARTTLS (587) and SSL (465)

**Emails going to spam?**
- Configure SPF record for your domain
- Configure DKIM record
- Use proper "From" address matching your domain

## Security Note

⚠️ **Important:** The password is currently in the code file. For production:
- Consider using environment variables
- Or use a config file outside web root
- Or use cPanel's password manager

