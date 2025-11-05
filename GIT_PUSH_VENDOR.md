# How to Push Vendor Folder to Repository

## Problem
The `vendor/` folder (containing PHPMailer) was being ignored by `.gitignore` and wasn't pushed to your repository.

## Solution
I've updated `.gitignore` to allow the `vendor/` folder. Now you need to add it to git.

## Steps to Push Vendor Folder

### Option 1: Using Git Commands

```bash
# Remove vendor from git cache (if it was previously ignored)
git rm -r --cached vendor/

# Add vendor folder
git add vendor/

# Add updated .gitignore
git add .gitignore

# Commit
git commit -m "Add PHPMailer vendor folder and update .gitignore"

# Push
git push
```

### Option 2: Force Add (if needed)

If the above doesn't work:

```bash
# Force add vendor folder
git add -f vendor/

# Add .gitignore
git add .gitignore

# Commit
git commit -m "Add PHPMailer vendor folder"

# Push
git push
```

### Option 3: Manual Upload (Alternative)

If you prefer not to push vendor to git (it's large), you can:

1. **Upload vendor folder directly via cPanel File Manager**
   - Navigate to your website root
   - Upload the entire `vendor/` folder
   - Or use FTP/SFTP to upload

2. **Or install via Composer on cPanel** (if Composer is available):
   ```bash
   composer require phpmailer/phpmailer
   ```

## Verify Vendor Folder

After pushing, verify the folder structure:
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

## Important Notes

- ✅ `.gitignore` now allows `vendor/` folder
- ✅ `config.php` is still ignored (for security)
- ✅ Vendor folder is needed on cPanel for the contact form to work
- ⚠️ Vendor folder is large (~2-3MB), but necessary for deployment

## Next Steps

1. Push vendor folder using one of the methods above
2. Upload everything to cPanel
3. Test the contact form
4. Verify emails are being sent

