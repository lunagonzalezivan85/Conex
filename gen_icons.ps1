# Genera iconos de la app con la paleta CONEX (navy/turquesa).
# C blanca + x turquesa sobre fondo navy.
Add-Type -AssemblyName System.Drawing

$res = "$PSScriptRoot\app-conex\android\app\src\main\res"
$navy  = [System.Drawing.Color]::FromArgb(255, 27, 42, 74)
$navy2 = [System.Drawing.Color]::FromArgb(255, 42, 61, 99)
$turq  = [System.Drawing.Color]::FromArgb(255, 15, 198, 194)
$white = [System.Drawing.Color]::White

function New-Bmp([int]$size) {
    $b = New-Object System.Drawing.Bitmap($size, $size)
    $g = [System.Drawing.Graphics]::FromImage($b)
    $g.SmoothingMode = 'AntiAlias'
    $g.TextRenderingHint = 'AntiAlias'
    return @($b, $g)
}

function Draw-Cx([System.Drawing.Graphics]$g, [int]$size, [float]$fontScale) {
    $fs = $size * $fontScale
    $font = New-Object System.Drawing.Font('Arial', [float]$fs, [System.Drawing.FontStyle]::Bold, [System.Drawing.GraphicsUnit]::Pixel)
    $fmt = New-Object System.Drawing.StringFormat
    $fmt.Alignment = 'Center'; $fmt.LineAlignment = 'Center'
    $wC  = $g.MeasureString('C', $font).Width
    $wx  = $g.MeasureString('x', $font).Width
    $gap = $size * 0.02
    $total = $wC + $wx + $gap
    $xC = ($size - $total) / 2 + $wC / 2
    $xX = ($size + $total) / 2 - $wx / 2
    $yC = $size * 0.5
    $yX = $size * 0.5 + $size * 0.02
    $br = New-Object System.Drawing.SolidBrush($white)
    $brX = New-Object System.Drawing.SolidBrush($turq)
    $g.DrawString('C', $font, $br, [float]$xC, [float]$yC, $fmt)
    $g.DrawString('x', $font, $brX, [float]$xX, [float]$yX, $fmt)
    $font.Dispose(); $br.Dispose(); $brX.Dispose()
}

function New-NavyGrad([int]$size) {
    $r = New-Object System.Drawing.Rectangle(0, 0, $size, $size)
    return New-Object System.Drawing.Drawing2D.LinearGradientBrush($r, $navy, $navy2, 45)
}

# --- Adaptive foreground (transparente, 432px = 108dp xxxhdpi) ---
$fgSizes = @{ 'mdpi'=108; 'hdpi'=162; 'xhdpi'=216; 'xxhdpi'=324; 'xxxhdpi'=432 }
foreach ($d in $fgSizes.GetEnumerator()) {
    $dir = "$res\mipmap-$($d.Key)"; New-Item -ItemType Directory -Force -Path $dir | Out-Null
    $sz = $d.Value
    $bmp, $g = New-Bmp $sz
    Draw-Cx $g $sz 0.30
    $bmp.Save("$dir\ic_launcher_foreground.png", 'Png'); $g.Dispose(); $bmp.Dispose()
    Write-Host "foreground $($d.Key)"
}

# --- Legacy icon (navy gradiente completo) ---
$legacySizes = @{ 'mdpi'=48; 'hdpi'=72; 'xhdpi'=96; 'xxhdpi'=144; 'xxxhdpi'=192 }
foreach ($d in $legacySizes.GetEnumerator()) {
    $dir = "$res\mipmap-$($d.Key)"; New-Item -ItemType Directory -Force -Path $dir | Out-Null
    $sz = $d.Value
    # square
    $bmp, $g = New-Bmp $sz
    $grad = New-NavyGrad $sz
    $g.FillRectangle($grad, 0, 0, $sz, $sz); $grad.Dispose()
    Draw-Cx $g $sz 0.34
    $bmp.Save("$dir\ic_launcher.png", 'Png'); $g.Dispose(); $bmp.Dispose()
    # round
    $bmp, $g = New-Bmp $sz
    $path = New-Object System.Drawing.Drawing2D.GraphicsPath
    $path.AddEllipse(0, 0, $sz, $sz)
    $g.SetClip($path)
    $grad = New-NavyGrad $sz
    $g.FillRectangle($grad, 0, 0, $sz, $sz); $grad.Dispose()
    Draw-Cx $g $sz 0.34
    $g.ResetClip()
    $bmp.Save("$dir\ic_launcher_round.png", 'Png'); $g.Dispose(); $bmp.Dispose()
    $path.Dispose()
    Write-Host "legacy $($d.Key)"
}

# --- background color para adaptive icon ---
$vals = "$res\values"; New-Item -ItemType Directory -Force -Path $vals | Out-Null
Set-Content "$vals\ic_launcher_background.xml" @'
<?xml version="1.0" encoding="utf-8"?>
<resources>
    <color name="ic_launcher_background">#1B2A4A</color>
</resources>
'@ -Encoding UTF8
Write-Host "background xml navy"

Write-Host "OK - iconos regenerados"

