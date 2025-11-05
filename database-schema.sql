-- Optional: Database Schema for Contact Form Submissions
-- This is NOT required if you only want to send emails
-- Use this if you want to store form submissions in a database

-- Database Schema for Dauzi Consulting Contact Form
-- Database: dauzicon_db
-- User: dauzicon_user

-- Note: Database should already exist. If not, create it in cPanel/phpMyAdmin first.
-- CREATE DATABASE IF NOT EXISTS dauzicon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE dauzicon_db;

-- Create table for contact form submissions
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

-- Note: To use this database:
-- 1. Create the database in phpMyAdmin or via command line
-- 2. Import this SQL file
-- 3. Uncomment the database code section in contact-handler.php
-- 4. Update the database credentials in contact-handler.php

