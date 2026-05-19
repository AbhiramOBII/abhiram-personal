<?php
/**
 * DayOS PWA Icon Generator (no GD required)
 * Run once: php generate-icons.php
 * Creates PNG icons with dark background + white "D" + teal dot.
 * Uses raw PNG creation with PHP zlib.
 */

$sizes = [72, 96, 128, 144, 152, 192, 384, 512];
$outputDir = __DIR__ . '/public/icons';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Colors (RGB)
$bg = [23, 22, 20];        // #171614
$teal = [79, 152, 163];    // #4f98a3
$white = [255, 255, 255];

function createPng($width, $height, $pixels) {
    // PNG signature
    $png = "\x89PNG\r\n\x1a\n";
    
    // IHDR chunk
    $ihdr = pack('N', $width) . pack('N', $height) . "\x08\x02\x00\x00\x00"; // 8-bit RGB
    $png .= pngChunk('IHDR', $ihdr);
    
    // IDAT chunk - raw pixel data with filter bytes
    $rawData = '';
    for ($y = 0; $y < $height; $y++) {
        $rawData .= "\x00"; // filter: none
        for ($x = 0; $x < $width; $x++) {
            $idx = ($y * $width + $x) * 3;
            $rawData .= chr($pixels[$idx]) . chr($pixels[$idx+1]) . chr($pixels[$idx+2]);
        }
    }
    $compressed = gzcompress($rawData);
    $png .= pngChunk('IDAT', $compressed);
    
    // IEND chunk
    $png .= pngChunk('IEND', '');
    
    return $png;
}

function pngChunk($type, $data) {
    $chunk = $type . $data;
    return pack('N', strlen($data)) . $chunk . pack('N', crc32($chunk));
}

function drawCircle(&$pixels, $width, $cx, $cy, $r, $color) {
    $r2 = $r * $r;
    for ($dy = -$r; $dy <= $r; $dy++) {
        for ($dx = -$r; $dx <= $r; $dx++) {
            if ($dx*$dx + $dy*$dy <= $r2) {
                $px = (int)($cx + $dx);
                $py = (int)($cy + $dy);
                if ($px >= 0 && $px < $width && $py >= 0 && $py < $width) {
                    $idx = ($py * $width + $px) * 3;
                    $pixels[$idx] = $color[0];
                    $pixels[$idx+1] = $color[1];
                    $pixels[$idx+2] = $color[2];
                }
            }
        }
    }
}

// Simple "D" shape as a bitmap pattern (relative coordinates 0-1)
function drawD(&$pixels, $size, $color) {
    // Draw a simple D letterform using rectangles and curves
    $cx = $size / 2;
    $cy = $size / 2;
    $scale = $size * 0.35;
    
    // Vertical bar of D (left side)
    $barLeft = (int)($cx - $scale * 0.5);
    $barRight = (int)($cx - $scale * 0.25);
    $barTop = (int)($cy - $scale * 0.7);
    $barBottom = (int)($cy + $scale * 0.7);
    
    for ($y = $barTop; $y <= $barBottom; $y++) {
        for ($x = $barLeft; $x <= $barRight; $x++) {
            if ($x >= 0 && $x < $size && $y >= 0 && $y < $size) {
                $idx = ($y * $size + $x) * 3;
                $pixels[$idx] = $color[0];
                $pixels[$idx+1] = $color[1];
                $pixels[$idx+2] = $color[2];
            }
        }
    }
    
    // Top horizontal bar
    $hLeft = (int)($cx - $scale * 0.5);
    $hRight = (int)($cx + $scale * 0.15);
    $hTop = (int)($cy - $scale * 0.7);
    $hBottom = (int)($cy - $scale * 0.45);
    
    for ($y = $hTop; $y <= $hBottom; $y++) {
        for ($x = $hLeft; $x <= $hRight; $x++) {
            if ($x >= 0 && $x < $size && $y >= 0 && $y < $size) {
                $idx = ($y * $size + $x) * 3;
                $pixels[$idx] = $color[0];
                $pixels[$idx+1] = $color[1];
                $pixels[$idx+2] = $color[2];
            }
        }
    }
    
    // Bottom horizontal bar
    $hTop2 = (int)($cy + $scale * 0.45);
    $hBottom2 = (int)($cy + $scale * 0.7);
    
    for ($y = $hTop2; $y <= $hBottom2; $y++) {
        for ($x = $hLeft; $x <= $hRight; $x++) {
            if ($x >= 0 && $x < $size && $y >= 0 && $y < $size) {
                $idx = ($y * $size + $x) * 3;
                $pixels[$idx] = $color[0];
                $pixels[$idx+1] = $color[1];
                $pixels[$idx+2] = $color[2];
            }
        }
    }
    
    // Curved right side of D (approximated with a semicircle)
    $arcCx = (int)($cx + $scale * 0.05);
    $arcCy = (int)$cy;
    $arcR = (int)($scale * 0.7);
    $innerR = (int)($scale * 0.45);
    
    for ($y = 0; $y < $size; $y++) {
        for ($x = (int)($cx - $scale * 0.1); $x < $size; $x++) {
            $dx = $x - $arcCx;
            $dy = $y - $arcCy;
            $dist = sqrt($dx*$dx + $dy*$dy);
            if ($dist <= $arcR && $dist >= $innerR && $dx >= 0) {
                $idx = ($y * $size + $x) * 3;
                $pixels[$idx] = $color[0];
                $pixels[$idx+1] = $color[1];
                $pixels[$idx+2] = $color[2];
            }
        }
    }
}

foreach ($sizes as $size) {
    // Initialize pixel array with background color
    $totalPixels = $size * $size * 3;
    $pixels = array_fill(0, $totalPixels, 0);
    
    for ($i = 0; $i < $size * $size; $i++) {
        $pixels[$i * 3] = $bg[0];
        $pixels[$i * 3 + 1] = $bg[1];
        $pixels[$i * 3 + 2] = $bg[2];
    }
    
    // Draw white "D"
    drawD($pixels, $size, $white);
    
    // Draw teal dot accent
    $dotR = max(2, (int)($size * 0.045));
    $dotX = (int)($size * 0.72);
    $dotY = (int)($size * 0.68);
    drawCircle($pixels, $size, $dotX, $dotY, $dotR, $teal);
    
    // Generate PNG
    $pngData = createPng($size, $size, $pixels);
    $outputPath = $outputDir . '/icon-' . $size . '.png';
    file_put_contents($outputPath, $pngData);
    
    echo "Generated: icon-{$size}.png (" . strlen($pngData) . " bytes)\n";
}

echo "\nAll icons generated in public/icons/\n";
