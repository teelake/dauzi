# How to Push Vendor Folder to Repository

## Problem
The `vendor/` folder (containing PHPMailer) wasn't pushed to your repository because it's large and was being ignored.

## Solution
The `.gitignore` has been updated to allow the vendor folder. Now push it:

## Steps to Push Vendor Folder

### Option 1: Add and Push (Recommended)

```bash
# Add vendor folder
git add vendor/

# Verify it's added
git status

# Commit
git commit -m "Add PHPMailer vendor folder"

# Push
git push
```

### Option 2: Force Add (if needed)

If git still ignores it:

```bash
# Force add vendor folder
git add -f vendor/

# Commit
git commit -m "Add PHPMailer vendor folder"

# Push
git push
```

### Option 3: Check File Size

If vendor folder is too large (>100MB), you might need Git LFS:

```bash
# Install Git LFS (if not installed)
git lfs install

# Track vendor folder
git lfs track "vendor/**"

# Add files
git add .gitattributes
git add vendor/

# Commit and push
git commit -m "Add PHPMailer vendor folder with LFS"
git push
```

## Verify After Push

After pushing, verify on GitHub/GitLab:
1. Check that `vendor/` folder appears in repository
2. Check that `vendor/autoload.php` exists
3. Check that `vendor/phpmailer/phpmailer/src/PHPMailer.php` exists

## Upload to cPanel

After pushing to repository:
1. Pull/Clone on cPanel server, OR
2. Upload `vendor/` folder directly via cPanel File Manager or FTP

## Folder Structure

The correct structure should be:
```
vendor/
├── autoload.php
├── composer/
└── phpmailer/
    └── phpmailer/
        └── src/
            ├── PHPMailer.php
            ├── SMTP.php
            └── Exception.php
```

This is the standard Composer structure with double `phpmailer` folder.

## Alternative: Manual Upload

If pushing vendor folder is too large, you can:
1. Upload `vendor/` folder directly to cPanel via File Manager
2. Or use FTP/SFTP to upload the entire `vendor/` folder
3. Make sure it's in the `/beta/` directory on your server

