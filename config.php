<?php
// Secure Configuration File for Dauzi Consulting
// DO NOT commit this file to public repositories
// Keep this file outside public_html if possible, or protect with .htaccess

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'dauzicon_db');
define('DB_USER', 'dauzicon_user');
define('DB_PASS', '*#Tsk1qT_8a)I,xy');

// Email Configuration
define('SMTP_HOST', 'mail.dauziconsulting.com'); // or 'localhost' for cPanel
define('SMTP_PORT', 587); // 587 for STARTTLS, 465 for SSL
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
define('SMTP_USER', 'no-reply@dauziconsulting.com');
define('SMTP_PASS', 'Temp_Pass123');

// Email Recipients
define('EMAIL_PRIMARY', 'info@dauziconsulting.com');
define('EMAIL_SECONDARY', 'dauziconsulting@gmail.com');
define('EMAIL_FROM', 'no-reply@dauziconsulting.com');
define('EMAIL_FROM_NAME', 'Dauzi Consulting Contact Form');

// Security
define('ENABLE_DATABASE', true); // Set to false if you don't want database storage
?>

