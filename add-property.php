<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Function to add watermark to an image
function addWatermark($sourceImagePath, $destinationImagePath)
{
    // Ensure the source image exists
    if (!file_exists($sourceImagePath)) {
        echo "Source image file does not exist: $sourceImagePath<br>";
        return false;
    }

    // Determine image type and load accordingly
    $imageType = exif_imagetype($sourceImagePath);
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($sourceImagePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($sourceImagePath);
            break;
        default:
            echo "Unsupported image type: $imageType<br>";
            return false;
    }

    if (!$image) {
        echo "Failed to load source image. Check if the file is a valid image.<br>";
        return false;
    }

    // Load the watermark
    $watermark = imagecreatefrompng('./assets/watermark.png');
    if (!$watermark) {
        echo "Failed to load watermark image. Ensure the watermark file exists and is a valid PNG image.<br>";
        imagedestroy($image);
        return false;
    }

    // Get dimensions of the source image and watermark
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $watermarkWidth = imagesx($watermark);
    $watermarkHeight = imagesy($watermark);

    // Calculate position for the watermark to be centered
    $x = ($imageWidth - $watermarkWidth) / 2;
    $y = ($imageHeight - $watermarkHeight) / 2;

    // Merge the watermark onto the image
    if (!imagecopy($image, $watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight)) {
        echo "Failed to merge watermark onto the image.<br>";
        imagedestroy($image);
        imagedestroy($watermark);
        return false;
    }

    // Save the image with watermark
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $saved = imagejpeg($image, $destinationImagePath);
            break;
        case IMAGETYPE_PNG:
            $saved = imagepng($image, $destinationImagePath);
            break;
    }

    if (!$saved) {
        echo "Failed to save the image with watermark.<br>";
        imagedestroy($image);
        imagedestroy($watermark);
        return false;
    }

    // Free up memory
    imagedestroy($image);
    imagedestroy($watermark);

    return true;
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property = $_POST;

    // echo '<pre>';
    // print_r($property);
    // echo '</pre>';

    $photo = $_FILES['photo'];
    $floorPlan = $_FILES['floorPlan'];

    // Define paths to save temporary images
    $parentDir = __DIR__ . '/tmp';

    // Ensure the temporary directory exists
    if (!is_dir($parentDir)) {
        if (!mkdir($parentDir, 0777, true)) {
            echo "Failed to create temporary directory: $parentDir<br>";
            exit();
        }
    }

    // Define paths for uploaded files
    $photoTmpPath = $parentDir . '/' . basename($photo['name']);
    $floorPlanTmpPath = $parentDir . '/' . basename($floorPlan['name']);

    // Check file upload errors
    if ($photo['error'] !== UPLOAD_ERR_OK) {
        echo "Photo upload error: " . $photo['error'] . "<br>";
    }

    if ($floorPlan['error'] !== UPLOAD_ERR_OK) {
        echo "Floor plan upload error: " . $floorPlan['error'] . "<br>";
    }

    // Move uploaded files to temporary directory
    if (!move_uploaded_file($photo['tmp_name'], $photoTmpPath)) {
        echo "Failed to move photo to tmp directory.<br>";
    }

    if (!move_uploaded_file($floorPlan['tmp_name'], $floorPlanTmpPath)) {
        echo "Failed to move floor plan to tmp directory.<br>";
    }

    // Verify file existence
    if (!file_exists($photoTmpPath)) {
        echo "Source image file does not exist: $photoTmpPath<br>";
    }

    if (!file_exists($floorPlanTmpPath)) {
        echo "Source image file does not exist: $floorPlanTmpPath<br>";
    }

    // Add watermark to images
    if (
        file_exists($photoTmpPath) && file_exists($floorPlanTmpPath) &&
        addWatermark($photoTmpPath, $photoTmpPath) &&
        addWatermark($floorPlanTmpPath, $floorPlanTmpPath)
    ) {

        // Upload to Bitrix24
        $response = CRest::call('crm.item.add', [
            'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
            'fields' => [
                'TITLE' => $property['titleDeed'],
                'ufCrm13PropertyType' => $property['property_type'],
                'ufCrm13Size' => $property['size'],
                'ufCrm13UnitNo' => $property['unitNo'],
                'ufCrm13Furnished' => $property['furnished'],
                'ufCrm13Bedroom' => $property['bedrooms'],
                'ufCrm13Bathroom' => $property['bathrooms'],
                'ufCrm13Parking' => $property['parkingSpaces'],
                'ufCrm13TotalPlotSize' => $property['totalPlotSize'],
                'ufCrm13LotSize' => $property['lotSize'],
                'ufCrm13BuildupArea' => $property['buildUpArea'],
                'ufCrm13LayoutType' => $property['layoutType'],
                'ufCrm13ProjectName' => $property['projectName'],
                'ufCrm13ProjectStatus' => $property['projectStatus'],
                'ufCrm13Ownership' => $property['ownership'],
                'ufCrm13Developers' => $property['developers'],
                'ufCrm13BuildYear' => $property['buildYear'],
                'ufCrm13Amenities' => $property['amenities'],

                'ufCrm13AgentName' => $property['listingAgent'],
                'ufCrm13ListingOwner' => $property['listingOwner'],
                'ufCrm13LandlordName' => $property['landlordName'],
                'ufCrm13LandlordEmail' => $property['landlordEmail'],
                'ufCrm13LandlordContact' => $property['landlordContact'],
                'ufCrm13Availability' => $property['availability'],
                'ufCrm13AvailableFrom' => $property['availableFrom'],

                'ufCrm13ReraPermitNumber' => $property['reraPermitNumber'],
                'ufCrm13ReraPermitIssueDate' => $property['reraPermitIssueDate'],
                'ufCrm13ReraPermitExpirationDate' => $property['reraPermitExpirationDate'],
                'ufCrm13DtcmPermitNumber' => $property['dtcmPermitNumber'],

                'ufCrm13TitleEn' => $property['title_english'],
                'ufCrm13DescriptionEn' => $property['description_english'],
                'ufCrm13TitleAr' => $property['title_arabic'],
                'ufCrm13DescriptionAr' => $property['description_arabic'],

                'ufCrm13Price' => $property['price'],
                'ufCrm13HidePrice' => $property['hidePrice'],
                'ufCrm13PaymentMethod' => $property['paymentMethod'],
                'ufCrm13DownPaymentPrice' => $property['downPayment'],
                'ufCrm13NoOfCheques' => $property['numCheques'],
                'ufCrm13ServiceCharge' => $property['serviceCharge'],
                'ufCrm13FinancialStatus' => $property['financialStatus'],
                'ufCrm13YearlyPrice' => $property['yearlyPrice'],
                'ufCrm13MonthlyPrice' => $property['monthlyPrice'],
                'ufCrm13WeeklyPrice' => $property['weeklyPrice'],
                'ufCrm13DailyPrice' => $property['dailyPrice'],

                'ufCrm13PhotoLinks' => [
                    $photoTmpPath,
                ],
                'ufCrm13VideoTourUrl' => $property['videoUrl'],
                'ufCrm_83_360_VIEW_URL' => $property['viewUrl'],
                'ufCrm13QrCodePropertyBooster' => $property['qrCode'],

                'ufCrm13Location' => $property['propertyLocation'],
                'ufCrm13City' => $property['propertyCity'],
                'ufCrm13Community' => $property['propertyCommunity'],
                'ufCrm13SubCommunity' => $property['propertySubCommunity'],
                'ufCrm13Tower' => $property['propertyTower'],
                'ufCrm13BayutLocation' => $property['bayutLocation'],
                'ufCrm13BayutCity' => $property['bayutCity'],
                'ufCrm13BayutCommunity' => $property['bayutCommunity'],
                'ufCrm13BayutSubCommunity' => $property['bayutSubCommunity'],
                'ufCrm13BayutTower' => $property['bayutTower'],
                'ufCrm13Latitude' => $property['latitude'],
                'ufCrm13Longitude' => $property['longitude'],
                'ufCrm13FloorPlan' => [
                    $floorPlanTmpPath
                ],

                'ufCrm13PfEnable' => $property['pfEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm13BayutEnable' => $property['bayutEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm13DubizleEnable' => $property['dubizleEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm13WebsiteEnable' => $property['websiteEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm13Status' => $property['publishing-status'] == 'pocket' ? 'POCKET_LISTING' : 'LIVE',
            ]
        ]);

        header("Location: index.php");
    } else {
        echo "Failed to add watermark to images.<br>";
    }
}
