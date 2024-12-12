<?php
require(__DIR__ . '/./vendor/autoload.php');

use Cloudinary\Cloudinary;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/.');
$dotenv->load();

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
    ],
]);

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $file = $_FILES['file'];
    $isDocument = isset($_POST['isDocument']) && $_POST['isDocument'] === 'true';

    try {
        // Corrected line: use $file['tmp_name'] instead of $file[['tmp_name']]
        $uploadResponse = $cloudinary->uploadApi()->upload($file['tmp_name'], [
            'folder' => 'livrichy-uploads',
            'resource_type' => $isDocument ? 'raw' : 'image',
        ]);

        echo json_encode(['url' => $uploadResponse['secure_url']]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Upload failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No file uploaded or file error. $_FILES: ' . json_encode($_FILES)]);
}
