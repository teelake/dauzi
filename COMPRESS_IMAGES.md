# Image Compression Guide for Hero Slider Images

## Current Image Sizes
- **Credible Advisory.png**: ~1.23 MB
- **Tax strategies.png**: ~1.06 MB  
- **Training that changes behavior.png**: ~1.16 MB

## Compression Options

### Option 1: PowerShell Script (Recommended)
Run the provided `compress-images.ps1` script:

```powershell
.\compress-images.ps1
```

This will:
- Compress all three hero slider images
- Maintain quality (85% quality)
- Create backups of originals
- Show compression statistics

### Option 2: Online Tools (Easiest - No Installation)

#### TinyPNG (Recommended)
1. Go to https://tinypng.com/
2. Upload all three PNG files:
   - `Credible Advisory.png`
   - `Tax strategies.png`
   - `Training that changes behavior.png`
3. Download compressed versions
4. Replace originals in `assets/img/` folder
5. Expected savings: 60-80% file size reduction

#### Squoosh (Advanced)
1. Go to https://squoosh.app/
2. Upload each image
3. Choose "OxiPNG" or "WebP" format
4. Adjust quality slider (recommend 85-90)
5. Download and replace

### Option 3: ImageMagick (Best Quality)

Install ImageMagick first:
```powershell
# Using Chocolatey (if installed)
choco install imagemagick

# Or download from: https://imagemagick.org/script/download.php
```

Then run:
```powershell
cd assets/img
magick "Credible Advisory.png" -strip -quality 85 -define png:compression-level=9 "Credible Advisory_compressed.png"
magick "Tax strategies.png" -strip -quality 85 -define png:compression-level=9 "Tax strategies_compressed.png"
magick "Training that changes behavior.png" -strip -quality 85 -define png:compression-level=9 "Training that changes behavior_compressed.png"
```

### Option 4: Convert to WebP (Best Compression)

WebP format offers 25-35% better compression than PNG:

```powershell
# Using ImageMagick (if installed)
cd assets/img
magick "Credible Advisory.png" -quality 85 "Credible Advisory.webp"
magick "Tax strategies.png" -quality 85 "Tax strategies.webp"
magick "Training that changes behavior.png" -quality 85 "Training that changes behavior.webp"
```

Then update HTML to use `.webp` files with fallback:
```html
<picture>
  <source srcset="assets/img/Credible Advisory.webp" type="image/webp">
  <img src="assets/img/Credible Advisory.png" alt="...">
</picture>
```

## Recommended Approach

**For immediate results**: Use TinyPNG (Option 2) - it's the fastest and easiest.

**For best results**: Use ImageMagick (Option 3) or convert to WebP (Option 4).

## Expected Results

After compression, you should see:
- **File size reduction**: 60-80% smaller
- **Quality**: Visually identical (85-90% quality)
- **Load time**: Much faster page load
- **SEO**: Better page speed scores

## Quality Settings Guide

- **90-100%**: Maximum quality (larger files)
- **85-90%**: Recommended (good balance)
- **80-85%**: Good compression (minor quality loss)
- **Below 80%**: Noticeable quality loss (not recommended)

