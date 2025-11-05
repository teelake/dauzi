# Contact Form Setup Guide

## ✅ What's Been Done

1. **Subject field is now mandatory** - Added `required` attribute and visual indicator (*)
2. **Form sends emails to both addresses:**
   - Primary: info@dauziconsulting.com
   - Copy: dauziconsulting@gmail.com
3. **From email:** Uses no-reply@dauziconsulting.com
4. **Address updated:** Added "Oyo State, Nigeria" to contact information
5. **Google Maps:** Updated with your provided embed code (height: 400px)

## 📧 Email Configuration

The form handler (`contact-handler.php`) is configured to:
- Send emails using PHP's `mail()` function
- Use `no-reply@dauziconsulting.com` as the sender
- Send to both email addresses simultaneously
- Include all form fields in the email body

## 💾 Database Storage (Optional)

**Do you need to save to database?**

**Short answer:** No, it's optional. You can just use email.

**When you SHOULD use database:**
- You want to keep a backup of all submissions
- You want to track submission history
- You want to build an admin panel to view submissions
- You want analytics on submissions

**When you DON'T need database:**
- Email notifications are sufficient
- You don't need historical records
- You prefer simplicity

### If You Want to Use Database:

1. **Create the database:**
   - Open phpMyAdmin
   - Import the `database-schema.sql` file, OR
   - Run the SQL commands manually in phpMyAdmin

2. **Update credentials in `contact-handler.php`:**
   - Find the commented database section (around line 60)
   - Uncomment it
   - Update these values:
     ```php
     $db_host = 'localhost';
     $db_name = 'dauzi_consulting';  // Your database name
     $db_user = 'your_username';     // Your phpMyAdmin username
     $db_pass = 'your_password';     // Your phpMyAdmin password
     ```

3. **Test the form** to ensure database saves work

## 🚀 Testing the Form

1. Upload both files to your cPanel:
   - `contact.html` (or rename to `contact.php` if needed)
   - `contact-handler.php`

2. Make sure PHP is enabled on your server

3. Test the form:
   - Fill out all required fields
   - Submit and check both email inboxes
   - Check for success/error messages

## ⚠️ Important Notes

- **PHP mail() function:** Some hosting providers require additional configuration
- **Email deliverability:** If emails go to spam, you may need to:
  - Configure SPF/DKIM records for your domain
  - Use SMTP instead of PHP mail() (requires PHPMailer)
- **Security:** The form includes basic validation and sanitization
- **Spam protection:** Consider adding reCAPTCHA if you get spam

## 📝 Files Created

1. `contact-handler.php` - Form processing script
2. `database-schema.sql` - Optional database schema
3. `CONTACT_FORM_SETUP.md` - This guide

## 🔧 Alternative: Using SMTP (More Reliable)

If PHP mail() doesn't work well, consider using PHPMailer with SMTP:
- Better deliverability
- More reliable
- Requires SMTP credentials from your hosting provider

Let me know if you need help setting up SMTP instead!

