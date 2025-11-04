# Image Compression Script for Dauzi Consulting
# This script compresses PNG images while maintaining quality

$ErrorActionPreference = "Stop"

# Check if ImageMagick is available (optional - more advanced compression)
$useImageMagick = $false
try {
    $null = Get-Command magick -ErrorAction Stop
    $useImageMagick = $true
    Write-Host "ImageMagick found - will use for compression" -ForegroundColor Green
} catch {
    Write-Host "ImageMagick not found - using .NET compression" -ForegroundColor Yellow
}

$images = @(
    "assets/img/Credible Advisory.png",
    "assets/img/Tax strategies.png",
    "assets/img/Training that changes behavior.png"
)

foreach ($imagePath in $images) {
    if (Test-Path $imagePath) {
        $originalSize = (Get-Item $imagePath).Length
        $originalSizeMB = [math]::Round($originalSize / 1MB, 2)
        
        Write-Host "`nProcessing: $imagePath" -ForegroundColor Cyan
        Write-Host "Original size: $originalSizeMB MB" -ForegroundColor Yellow
        
        if ($useImageMagick) {
            # Using ImageMagick for better compression
            $outputPath = $imagePath -replace '\.png$', '_compressed.png'
            magick "$imagePath" -strip -quality 85 -define png:compression-level=9 "$outputPath"
            
            if (Test-Path $outputPath) {
                $newSize = (Get-Item $outputPath).Length
                $newSizeMB = [math]::Round($newSize / 1MB, 2)
                $savings = [math]::Round((1 - ($newSize / $originalSize)) * 100, 1)
                
                Write-Host "Compressed size: $newSizeMB MB" -ForegroundColor Green
                Write-Host "Space saved: $savings%" -ForegroundColor Green
                
                # Replace original with compressed version
                Move-Item -Path $outputPath -Destination $imagePath -Force
            }
        } else {
            # Using .NET for basic compression (requires System.Drawing)
            Add-Type -AssemblyName System.Drawing
            
            $img = [System.Drawing.Image]::FromFile((Resolve-Path $imagePath))
            $bitmap = New-Object System.Drawing.Bitmap($img.Width, $img.Height)
            $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
            $graphics.DrawImage($img, 0, 0)
            
            $codec = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq "image/png" }
            $encoderParams = New-Object System.Drawing.Imaging.EncoderParameters(1)
            $encoderParams.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, 85L)
            
            $outputPath = $imagePath -replace '\.png$', '_compressed.png'
            $bitmap.Save($outputPath, $codec, $encoderParams)
            
            $graphics.Dispose()
            $bitmap.Dispose()
            $img.Dispose()
            
            if (Test-Path $outputPath) {
                $newSize = (Get-Item $outputPath).Length
                $newSizeMB = [math]::Round($newSize / 1MB, 2)
                $savings = [math]::Round((1 - ($newSize / $originalSize)) * 100, 1)
                
                Write-Host "Compressed size: $newSizeMB MB" -ForegroundColor Green
                Write-Host "Space saved: $savings%" -ForegroundColor Green
                
                # Backup original and replace
                $backupPath = $imagePath -replace '\.png$', '_original.png'
                Copy-Item $imagePath $backupPath
                Move-Item -Path $outputPath -Destination $imagePath -Force
            }
        }
    } else {
        Write-Host "File not found: $imagePath" -ForegroundColor Red
    }
}

Write-Host "`nCompression complete!" -ForegroundColor Green

