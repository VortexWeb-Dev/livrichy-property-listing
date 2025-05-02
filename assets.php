<?php
$imageUrl = $_GET['imageUrl'] ?? '';
$watermarkUrl = $_GET['watermarkUrl'] ?? 'https://connecteo.in/livrichy-property-listing/assets/images/watermark.png';
$applyWatermark = $_GET['watermark'] == 1;

// Validate image URL
if (!$imageUrl) {
    http_response_code(400);
    echo 'Missing image URL.';
    exit;
}

// Get image metadata
$imageInfo = getimagesize($imageUrl);
if (!$imageInfo) {
    http_response_code(400);
    echo 'Invalid image URL.';
    exit;
}
$mime = $imageInfo['mime'];
$imageData = file_get_contents($imageUrl);
if (!$imageData) {
    http_response_code(404);
    echo 'Unable to fetch image.';
    exit;
}

// Create image from string
$srcImage = imagecreatefromstring($imageData);
if (!$srcImage) {
    http_response_code(500);
    echo 'Invalid image data.';
    exit;
}

// Apply watermark only if requested
if ($applyWatermark && $watermarkUrl) {
    $watermarkData = file_get_contents($watermarkUrl);
    if ($watermarkData) {
        $watermark = imagecreatefromstring($watermarkData);
        if ($watermark) {
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);

            $wmOriginalWidth = imagesx($watermark);
            $wmOriginalHeight = imagesy($watermark);
            $wmAspect = $wmOriginalWidth / $wmOriginalHeight;

            // Target watermark size (40% of original image)
            $targetWidth = $srcWidth * 0.4;
            $targetHeight = $srcHeight * 0.4;

            if ($wmAspect > ($targetWidth / $targetHeight)) {
                $newWmWidth = $targetWidth;
                $newWmHeight = $newWmWidth / $wmAspect;
            } else {
                $newWmHeight = $targetHeight;
                $newWmWidth = $newWmHeight * $wmAspect;
            }

            // Resize watermark
            $resizedWatermark = imagecreatetruecolor($newWmWidth, $newWmHeight);
            imagealphablending($resizedWatermark, false);
            imagesavealpha($resizedWatermark, true);
            imagecopyresampled(
                $resizedWatermark,
                $watermark,
                0,
                0,
                0,
                0,
                $newWmWidth,
                $newWmHeight,
                $wmOriginalWidth,
                $wmOriginalHeight
            );

            // Center watermark
            $dstX = ($srcWidth - $newWmWidth) / 2;
            $dstY = ($srcHeight - $newWmHeight) / 2;

            imagecopy($srcImage, $resizedWatermark, $dstX, $dstY, 0, 0, $newWmWidth, $newWmHeight);

            imagedestroy($watermark);
            imagedestroy($resizedWatermark);
        }
    }
}

// Output image
switch ($mime) {
    case 'image/jpeg':
        header('Content-Type: image/jpeg');
        imagejpeg($srcImage, null, 100);
        break;
    case 'image/png':
        header('Content-Type: image/png');
        imagepng($srcImage, null, 0);
        break;
    case 'image/webp':
        header('Content-Type: image/webp');
        imagewebp($srcImage, null, 100);
        break;
    default:
        http_response_code(415);
        echo 'Unsupported image type.';
        imagedestroy($srcImage);
        exit;
}

imagedestroy($srcImage);
