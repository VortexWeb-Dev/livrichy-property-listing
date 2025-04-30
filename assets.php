<?php
$imageUrl = $_GET['image'] ?? '';
$watermarkUrl = $_GET['watermark'] ?? './assets/images/watermark.png';

// Validate image URL
if (!$imageUrl) {
    http_response_code(400);
    echo 'Missing image URL.';
    exit;
}

$imageData = file_get_contents($imageUrl);
if (!$imageData) {
    http_response_code(404);
    echo 'Unable to fetch image.';
    exit;
}

$srcImage = imagecreatefromstring($imageData);
if (!$srcImage) {
    http_response_code(500);
    echo 'Invalid image data.';
    exit;
}

// If watermark URL is provided and valid, apply it
if ($watermarkUrl) {
    $watermarkData = file_get_contents($watermarkUrl);
    if ($watermarkData) {
        $watermark = imagecreatefromstring($watermarkData);
        if ($watermark) {
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);
            $wmWidth = imagesx($watermark);
            $wmHeight = imagesy($watermark);

            $dstX = $srcWidth - $wmWidth - 10;
            $dstY = $srcHeight - $wmHeight - 10;

            imagecopy($srcImage, $watermark, $dstX, $dstY, 0, 0, $wmWidth, $wmHeight);
            imagedestroy($watermark);
        }
    }
}

// Output image
header('Content-Type: image/png');
imagepng($srcImage);
imagedestroy($srcImage);
