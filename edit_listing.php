<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve the properties from the session
session_start();
if (isset($_SESSION["properties"])) {
    $properties = $_SESSION["properties"];
}

$property_id = $_GET['id'];

$response = CRest::call('crm.item.get', [
    'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
    'id' => $property_id
]);

$property = $response['result']['item'] ?? null;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $files = $_FILES;

    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';

    // echo '<pre>';
    // print_r($files);
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

    $fields = [
        'TITLE' => $data['title'],

        'ufCrm13OfferingType' => $data['offer_type'] === 'rent' ? 'Rent' : 'Sale',

        // 'ufCrm13PropertyType' => $data[''],
        'ufCrm13FloorPlan' => $data[''],

        'ufCrm13Size' => $data['size'],
        'ufCrm13UnitNumber' => $data['unitNo'],
        'ufCrm13Bedroom' => $data['bedrooms'],
        'ufCrm13Bathroom' => $data['bathrooms'],
        'ufCrm13Parking' => $data['parkingSpaces'],
        'ufCrm13Furnished' => $data['furnished'],
        'ufCrm13TotalPlotSize' => $data['totalPlotSize'],
        'ufCrm13LotSize' => $data['lotSize'],
        'ufCrm13BuiltUpArea' => $data['buildUpArea'],
        'ufCrm13LayoutType' => $data['layoutType'],
        'ufCrm13ProjectName' => $data['projectName'],
        'ufCrm13ProjectStatus' => $data['projectStatus'],
        'ufCrm13Ownership' => $data['ownership'],
        'ufCrm13Developers' => $data['developers'],
        'ufCrm13BuildYear' => $data['buildYear'],
        'ufCrm13ListingAgent' => $data['listingAgent'],
        'ufCrm13ListingOwner' => $data['listingOwner'],
        'ufCrm13LandlordName' => $data['landlordName'],
        'ufCrm13EmailAddress' => $data['landlordEmail'],
        'ufCrm13PhoneNumber' => $data['landlordContact'],
        'ufCrm13Availability' => $data['availability'],
        'ufCrm13AvailableFrom' => $data['availableFrom'],
        'ufCrm13Price' => $data['price'],
        'ufCrm13HidePrice' => $data['hidePrice'] ? "Y" : "N",
        'ufCrm13PaymentMethod' => $data['paymentMethod'],
        'ufCrm13DownPaymentPrice' => $data['downPayment'],
        'ufCrm13NoOfCheques' => $data['numCheques'],
        'ufCrm13ufCrm13ServiceCharge' => $data['serviceCharge'],
        'ufCrm13ufCrm13FinancialStatus' => $data['financialStatus'],
        'ufCrm13YearlyPrice' => $data['yearlyPrice'],
        'ufCrm13MonthlyPrice' => $data['monthlyPrice'],
        'ufCrm13WeeklyPrice' => $data['weeklyPrice'],
        'ufCrm13DailyPrice' => $data['dailyPrice'],
        'ufCrm13TitleEn' => $data['title_english'],
        'ufCrm13DescriptionEn' => $data['description_english'],
        'ufCrm13TitleAr' => $data['title_arabic'],
        'ufCrm13DescriptionAr' => $data['description_arabic'],
        'ufCrm13VideoTourUrl' => $data['videoUrl'],
        'ufCrm_42_360_VIEW_URL' => $data['viewUrl'],
        'ufCrm13QrCodePropertyBooster' => $data['qrCode'],
        'ufCrm13BayutLocation' => $data['bayutLocation'],
        'ufCrm13BayutCity' => $data['bayutCity'],
        'ufCrm13BayutCommunity' => $data['bayutCommunity'],
        'ufCrm13BayutSubCommunity' => $data['bayutSubCommunity'],
        'ufCrm13BayutTower' => $data['bayutTower'],
        'ufCrm13Latitude' => $data['latitude'],
        'ufCrm13Longitude' => $data['longitude'],
        'ufCrm13PfEnable' => $data['pfEnable'] ? 'Y' : 'N',
        'ufCrm13BayutEnable' => $data['bayutEnable'] ? 'Y' : 'N',
        'ufCrm13DubizleEnable' => $data['dubizleEnable'] ? 'Y' : 'N',
        'ufCrm13WebsiteEnable' => $data['websiteEnable'] ? 'Y' : 'N',
        'ufCrm13Location' => $data['propertyLocation'],
        'ufCrm13City' => $data['propertyCity'],
        'ufCrm13Community' => $data['propertyCommunity'],
        'ufCrm13SubCommunity' => $data['propertySubCommunity'],
        'ufCrm13Tower' => $data['propertyTower'],
        'ufCrm13Status' => $data['DRAFT'],
        'ufCrm13ReraPermitNumber' => $data['reraPermitNumber'],
        'ufCrm13ReraPermitIssueDate' => $data['reraPermitIssueDate'],
        'ufCrm13ReraPermitExpirationDate' => $data['reraPermitExpirationDate'],
        'ufCrm13DtcmPermitNumber' => $data['dtcmPermitNumber'],
        'ufCrm13HidePrice' => $data['hidePrice'] ? 'Y' : 'N',
    ];

    $result = CRest::call('crm.item.update', [
        'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
        'id' => $property_id,
        'fields' => $fields
    ]);

    header('Location: ' . $_SERVER['PHP_SELF']);

    // echo '<pre>';
    // print_r($result);
    // echo '</pre>';
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .settings-menu,
        .bulk-options-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .nav-pills .nav-link {
            border-radius: 50px;
        }

        .nav-pills .nav-link.completed {
            background-color: #28a745;
            /* Green */
            color: white;
        }

        .nav-pills .nav-link.active {
            background-color: #007bff;
            /* Blue */
            color: white;
        }

        .nav-pills .nav-link:not(.active):not(.completed) {
            background-color: #e9ecef;
            /* Light gray */
        }

        /* Hide the radio buttons */
        /* input[type="radio"] {
			display: none;
		} */

        /* Label styling for unselected state */
        label {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 10px;
            border-radius: 10px;
        }

        /* Styling for selected radio buttons */
        input[type="radio"]:checked+img {
            padding: 5px;
            border: 2px solid blue;
            border-radius: 10px;
        }

        /* Additional styling for text under images */
        label img {
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <form class="d-flex" method="post" enctype="multipart/form-data" id="mainForm">
        <!-- Sticky Left Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <!-- Fixed Topbar -->
            <?php include 'includes/topbar.php'; ?>

            <!-- <form action="media.php" method="POST"> -->
            <div class="container-fluid px-5 py-4">
                <h2 class="display-10 fw-bold text-primary container">Edit Property Details</h2>
                <div class="container mt-4">
                    <div class="row p-3">

                        <!-- Choose Property Type Section -->
                        <div class="col-12 col-md-9 bg-light p-3">
                            <h4>Choose Property Type</h4>
                            <div class="row align-items-center">
                                <!-- First Column: Commercial or Residential -->
                                <div class="col-5 d-flex justify-content-center align-items-center gap-3 mb-3">
                                    <div class="text-center">
                                        <label>
                                            <input type="radio" name="property_type" value="residential" required>
                                            <img width="50" height="50" src="./assets/house.png" alt="house">
                                            <br>Residential
                                        </label>
                                    </div>
                                    <div class="text-center">
                                        <label>
                                            <input type="radio" name="property_type" value="commercial" required>
                                            <img width="50" height="50" src="./assets/apartment.png" alt="apartment">
                                            <br>Commercial
                                        </label>
                                    </div>
                                </div>

                                <!-- Vertical Line -->
                                <div class="col-1 d-flex justify-content-center">
                                    <div class="vr" style="height: 100%;"></div>
                                </div>

                                <!-- Second Column: Rent or Sale -->
                                <div class="col-6 d-flex justify-content-center align-items-center gap-3 mb-3">
                                    <div class="text-center">
                                        <label>
                                            <input type="radio" name="offer_type" value="rent" required <?= $property['ufCrm13OfferingType'] == 'Rent' ? 'checked' : ''; ?>>
                                            <img width="50" height="50" src="./assets/rent.png" alt="rent">
                                            <br>Rent
                                        </label>
                                    </div>
                                    <div class="text-center">
                                        <label>
                                            <input type="radio" name="offer_type" value="sale" required <?= $property['ufCrm13OfferingType'] == 'Sale' ? 'checked' : ''; ?>>
                                            <img width="50" height="50" src="./assets/sale.png" alt="sale">
                                            <br>Sale
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Draft Property Section -->
                        <div class="col-12 col-md-3 bg-light p-3">
                            <h4>Draft Property</h4>
                            <p class="text-muted">Last Update: <?= date_format(date_create($property['updatedTime']), 'd/m/Y') ?></p>
                            <p class="text-muted">Creation Date: <?= date_format(date_create($property['createdTime']), 'd/m/Y') ?></p>
                            <p class="text-muted">Created By CRM ID: <?= $property['createdBy']; ?></p>
                        </div>
                    </div>

                    <div class="row mt-3 p-3">
                        <div class="col-12 col-md-9 bg-light p-3">
                            <h4>Specifications</h4>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="titleDeed">Title Deed</label><br>
                                    <input type="text" id="titleDeed" name="titleDeed" class="form-control" value="<?= $property['title']; ?>">
                                </div>
                                <div class="col-6">
                                    <label for="propertyType">Property Type <span class="text-danger">*</span></label><br>
                                    <select name="propertyType" id="propertyType" class="form-control" required>
                                        <option value="<?= $property['ufCrm13PropertyType']; ?>" class="form-control"><?= $property['ufCrm13PropertyType']; ?></option>
                                        <option value="AP" class="form-control">Apartment / Flat</option>
                                        <option value="TH" class="form-control">Townhouse</option>
                                        <option value="VH" class="form-control">Villa / House</option>
                                        <option value="PH" class="form-control">Penthouse</option>
                                        <option value="LP" class="form-control">Residential Land</option>
                                        <option value="FF" class="form-control">Full Floor</option>
                                        <option value="BU" class="form-control">Bulk Units</option>
                                        <option value="CD" class="form-control">Compound</option>
                                        <option value="DX" class="form-control">Duplex</option>
                                        <option value="FA" class="form-control">Factory</option>
                                        <option value="FA" class="form-control">Farm</option>
                                        <option value="HA" class="form-control">Hotel Apartment</option>
                                        <option value="HF" class="form-control">Half Floor</option>
                                        <option value="LC" class="form-control">Labor Camp</option>
                                        <option value="LP" class="form-control">Land / Plot</option>
                                        <option value="OF" class="form-control">Office Space</option>
                                        <option value="OF" class="form-control">Business Centre</option>
                                        <option value="RE" class="form-control">Retail</option>
                                        <option value="RE" class="form-control">Restaurant</option>
                                        <option value="SA" class="form-control">Staff Accommodation</option>
                                        <option value="WB" class="form-control">Whole Building</option>
                                        <option value="SH" class="form-control">Shop</option>
                                        <option value="SR" class="form-control">Show Room</option>
                                        <option value="OF" class="form-control">Co-working Space</option>
                                        <option value="WH" class="form-control">Storage</option>
                                        <option value="WH" class="form-control">Warehouse</option>
                                        <option value="LP" class="form-control">Commercial Land</option>
                                        <option value="FF" class="form-control">Commercial Floor</option>
                                        <option value="WB" class="form-control">Commercial Building</option>
                                        <option value="FF" class="form-control">Residential Floor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="size">Size (Sq.ft) <span class="text-danger">*</span></label><br>
                                    <input type="number" id="size" name="size" class="form-control" value="<?= $property['ufCrm13Size']; ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="unitNo">Unit No. <span class="text-danger">*</span></label><br>
                                    <input type="text" id="unitNo" name="unitNo" class="form-control" value="<?= $property['ufCrm13UnitNumber']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="bedrooms">No. of Bedrooms</label><br>
                                    <input type="number" id="bedrooms" name="bedrooms" value="<?= $property['ufCrm13Bedroom']; ?>" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="bathrooms">No. of Bathrooms</label><br>
                                    <input type="number" id="bathrooms" name="bathrooms" value="<?= $property['ufCrm13Bathroom']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="parkingSpaces">No. Parking Spaces</label><br>
                                    <input type="number" id="parkingSpaces" name="parkingSpaces" value="<?= $property['ufCrm13Parking']; ?>" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="furnished">Select Furnished</label><br>
                                    <select name="furnished" id="furnished" class="form-control">
                                        <option value="<?= $property['ufCrm13Furnished']; ?>"><?= $property['ufCrm13Furnished']; ?></option>
                                        <option value="unfurnished">Unfurnished</option>
                                        <option value="semi-furnished">Semi-furnished</option>
                                        <option value="furnished">Furnished</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="totalPlotSize">Total Plot Size (Sq.ft)</label><br>
                                    <input type="number" value="<?= $property['ufCrm13TotalPlotSize']; ?>" id="totalPlotSize" name="totalPlotSize" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="lotSize">Lot Size (Sq.ft)</label><br>
                                    <input type="number" value="<?= $property['ufCrm13LotSize']; ?>" id="lotSize" name="lotSize" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="buildUpArea">Build-up Area</label><br>
                                    <input type="text" value="<?= $property['ufCrm13BuildupArea']; ?>" id="buildUpArea" name="buildUpArea" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="layoutType">Layout Type</label><br>
                                    <input type="text" value="<?= $property['ufCrm13LayoutType']; ?>" id="layoutType" name="layoutType" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="projectName">Project Name</label><br>
                                    <input type="text" value="<?= $property['ufCrm13ProjectName']; ?>" id="projectName" name="projectName" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="projectStatus">Project Status</label><br>
                                    <input type="text" value="<?= $property['ufCrm13ProjectStatus']; ?>" id="projectStatus" name="projectStatus" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="ownership">Ownership</label><br>
                                    <select name="ownership" id="ownership" class="form-control">
                                        <option value="" class="form-control">Please select</option>
                                        <option value="free_hold" class="form-control">Free hold</option>
                                        <option value="none_hold" class="form-control">None hold</option>
                                        <option value="lease_hold" class="form-control">Lease hold</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="developers">Developers</label><br>
                                    <input type="text" value="<?= $property['ufCrm13Developers']; ?>" name="developers" id="developers" class="form-control">
                                    <!-- <select name="developers" id="developers" class="form-control">
								<option value="" class="form-control">Please select</option>
								<?php
                                // foreach ($developers as $developer) {
                                // echo '<option value="' . $developer['value'] . '" class="form-control">' . $developer['name'] . '</option>';
                                // }
                                ?>
							</select> -->
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="buildYear">Build Year</label><br>
                                    <input type="number" value="<?= $property['ufCrm13BuildYear']; ?>" id="buildYear" name="buildYear" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 bg-light p-3">
                            <h4>Management</h4>
                            <div class="mb-3">
                                <label for="reference">Reference</label>
                                <input type="text" value="<?= $property['ufCrm13ReferenceNumber']; ?>" id="reference" class="form-control" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="listingAgent">Listing Agent</label>
                                <input type="text" value="<?= $property['ufCrm13AgentName']; ?>" id="listingAgent" name="listingAgent" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="listingOwner">Listing Owner</label>
                                <input type="text" value="<?= $property['ufCrm13OwnerName']; ?>" id="listingOwner" name="listingOwner" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="landlordName">Landlord Name</label>
                                <input type="text" value="<?= $property['ufCrm13OwnerName']; ?>" id="landlordName" name="landlordName" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="landlordEmail">Landlord Email</label>
                                <input type="text" value="<?= $property['ufCrm13EmailAddress']; ?>" id="landlordEmail" name="landlordEmail" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="landlordContact">Landlord Contact</label>
                                <input type="text" value="<?= $property['ufCrm13PhoneNumber']; ?>" id="landlordContact" name="landlordContact" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="availability">Availability</label><br>
                                <select name="availability" id="availability" class="form-control">
                                    <option value="<?= $property['ufCrm13Availability']; ?>" class="form-control"><?= $property['ufCrm13Availability']; ?></option>
                                    <option value="available" class="form-control">Available</option>
                                    <option value="underOffer" class="form-control">Under Offer</option>
                                    <option value="reserved" class="form-control">Reserved</option>
                                    <option value="sold" class="form-control">Sold</option>
                                </select>

                            </div>
                            <div class="mb-3">
                                <label for="availableFrom">Available from</label>
                                <input type="date" value="<?= date_format(new DateTime($property['ufCrm13AvailableFrom']), 'Y-m-d') ?>" id="availableFrom" name="availableFrom" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 p-3">
                        <!-- Pricing Section -->
                        <div class="col-12 col-md-9 bg-light p-3 rounded">
                            <h4 class="mb-3">Pricing</h4>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" value="<?= $property['ufCrm13Price'] ?>" id="price" name="price" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="hidePrice" class="form-label">Hide Price (Property Finder only)</label>
                                    <input type="checkbox" id="hidePrice" name="hidePrice" class="form-check-input" checked="<?= $property['ufCrm13HidePrice'] == 'Y' ? 'checked' : '' ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="paymentMethod" class="form-label">Payment Method</label>
                                    <input type="text" value="<?= $property['ufCrm13PaymentMethod'] ?>" id="paymentMethod" name="paymentMethod" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="downPayment" class="form-label">Down Payment Price</label>
                                    <input type="text" value="<?= $property['ufCrm13DownPaymentPrice'] ?>" id="downPayment" name="downPayment" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="numCheques" class="form-label">No. of Cheques</label>
                                    <select id="numCheques" name="numCheques" class="form-control">
                                        <option value="<?= $property['ufCrm13NoOfCheques'] ?>"><?= $property['ufCrm13NoOfCheques'] ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="serviceCharge" class="form-label">Service Charge</label>
                                    <input type="text" value="<?= $property['ufCrm13ServiceCharge'] ?>" id="serviceCharge" name="serviceCharge" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="financialStatus" class="form-label">Financial Status</label>
                                    <select id="financialStatus" name="financialStatus" value="<?= $property['ufCrm13FinancialStatus'] ?>" class="form-control">
                                        <option value="">Please select</option>
                                        <option value="morgaged">Morgaged</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Property Permit Section -->
                        <div class="col-12 col-md-3 bg-light p-3 rounded">
                            <h4 class="mb-3">Property Permit</h4>

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs" id="permitTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="rera-tab" data-bs-toggle="tab" href="#rera" role="tab">RERA</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="dtcm-tab" data-bs-toggle="tab" href="#dtcm" role="tab">DTCM</a>
                                </li>
                            </ul>

                            <!-- Tabs Content -->
                            <div class="tab-content mt-3" id="permitTabsContent">
                                <div class="tab-pane fade show active" id="rera" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="reraPermitNumber" class="form-label">RERA Permit Number</label>
                                        <input type="text" value="<?= $property['ufCrm13ReraPermitNumber'] ?>" id="reraPermitNumber" name="reraPermitNumber" class="form-control">
                                        <p class="text-muted text-sm">Once the reference is added it cannot be reassigned</p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reraPermitIssueDate" class="form-label">RERA Permit Issue Date</label>
                                        <input type="date" value="<?= date_format(new DateTime($property['ufCrm13ReraPermitIssueDate']), 'Y-m-d') ?>" id="reraPermitIssueDate" name="reraPermitIssueDate" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="reraPermitExpirationDate" class="form-label">RERA Permit Expiration Date</label>
                                        <input type="date" value="<?= date_format(new DateTime($property['ufCrm13ReraPermitExpirationDate']), 'Y-m-d') ?>" id="reraPermitExpirationDate" name="reraPermitExpirationDate" class="form-control">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="dtcm" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="dtcmPermitNumber" class="form-label">DTCM Permit Number</label>
                                        <input type="text" value="<?= $property['ufCrm13DtcmPermitNumber'] ?>" id="dtcmPermitNumber" name="dtcmPermitNumber" class="form-control">
                                        <p class="text-muted text-sm">Once the reference is added it cannot be reassigned</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 p-3">
                        <!-- Pricing Section -->
                        <div class="col-12 col-md-9 bg-light p-3 rounded">
                            <h4 class="mb-3">Pricing (If Rent)</h4>

                            <!-- Radio Buttons for Pricing Options -->
                            <div class="pricing-option d-flex align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pricing" id="yearly" value="yearly" checked>
                                    <label class="form-check-label" for="yearly">
                                        Yearly
                                    </label>
                                </div>
                                <input type="number" value="<?= $property['ufCrm13YearlyPrice'] ?>" class="form-control ms-auto" id="yearlyPrice" name="yearlyPrice" placeholder="Price">
                            </div>

                            <div class="pricing-option d-flex align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pricing" id="monthly" value="monthly">
                                    <label class="form-check-label" for="monthly">
                                        Monthly
                                    </label>
                                </div>
                                <input type="number" value="<?= $property['ufCrm13MonthlyPrice'] ?>" class="form-control ms-auto" id="monthlyPrice" name="monthlyPrice" placeholder="Price">
                            </div>

                            <div class="pricing-option d-flex align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pricing" id="weekly" value="weekly">
                                    <label class="form-check-label" for="weekly">
                                        Weekly
                                    </label>
                                </div>
                                <input type="number" value="<?= $property['ufCrm13WeeklyPrice'] ?>" class="form-control ms-auto" id="weeklyPrice" name="weeklyPrice" placeholder="Price">
                            </div>

                            <div class="pricing-option d-flex align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pricing" id="daily" value="daily">
                                    <label class="form-check-label" for="daily">
                                        Daily
                                    </label>
                                </div>
                                <input type="number" value="<?= $property['ufCrm13DailyPrice'] ?>" class="form-control ms-auto" id="dailyPrice" name="dailyPrice" placeholder="Price">
                            </div>

                            <!-- Other Pricing Details -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="paymentMethod" class="form-label">Payment Method</label>
                                    <input type="text" value="<?= $property['ufCrm13PaymentMethod'] ?>" id="paymentMethod" name="paymentMethod" class="form-control">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="numCheques" class="form-label">No. of Cheques</label>
                                    <select id="numCheques" name="numCheques" class="form-control">
                                        <option value="<?= $property['ufCrm13NoOfCheques'] ?>"><?= $property['ufCrm13NoOfCheques'] ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="serviceCharge" class="form-label">Service Charge</label>
                                    <input type="text" value="<?= $property['ufCrm13ServiceCharge'] ?>" id="serviceCharge" name="serviceCharge" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="financialStatus" class="form-label">Financial Status</label>
                                    <select id="financialStatus" name="financialStatus" class="form-control">
                                        <option value="<?= $property['ufCrm13FinancialStatus'] ?>"><?= $property['ufCrm13FinancialStatus'] ?></option>
                                        <option value="morgaged">Morgaged</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-3 p-3">
                        <div class="col-12 col-md-6 bg-light p-3 rounded">
                            <h4 class="mb-3">Description</h4>

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs mb-3" id="languageTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="english-tab" data-bs-toggle="tab" href="#english" role="tab">English</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="arabic-tab" data-bs-toggle="tab" href="#arabic" role="tab">Arabic</a>
                                </li>
                            </ul>

                            <!-- Tabs Content -->
                            <div class="tab-content" id="languageTabsContent">
                                <div class="tab-pane fade show active" id="english" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="title_english" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" value="<?= $property['ufCrm13TitleEn'] ?>" id="title_english" name="title_english" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description_english" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea id="description_english" name="description_english" class="form-control" placeholder="Enter English description" required><?= $property['ufCrm13DescriptionEn'] ?></textarea>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="arabic" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="title_arabic" class="form-label">Title</label>
                                        <input type="text" value="<?= $property['ufCrm13TitleAr'] ?>" id="title_arabic" name="title_arabic" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="description_arabic" class="form-label">Description</label>
                                        <textarea id="description_arabic" name="description_arabic" class="form-control" placeholder="Enter Arabic description"><?= $property['ufCrm13DescriptionAr'] ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="mb-3">
                                <p class="h5 mb-3">Select Amenities</p>

                             
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#amenitiesModal">
                                    Update Amenities
                                </button>

                             
                                <div id="selectedAmenities" class="mt-3"></div>
                            </div> -->

                            <!-- Modal Structure -->
                            <div class="modal fade" id="amenitiesModal" tabindex="-1" aria-labelledby="amenitiesModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="amenitiesModalLabel">Select Amenities</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Private Amenities Section -->
                                                <div class="col-12">
                                                    <p class="h6 mb-3">Private Amenities</p>
                                                    <div id="amenities-container" class="row gy-2">
                                                        <!-- Amenities will be inserted here by JavaScript -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="saveAmenities">Save Amenities</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="container mt-5 p-3">
                        <div class="bg-light p-4 rounded border">
                            <h4 class="mb-4">Add Photos</h4>
                            <div class="text-center mb-3">
                                <input type="file" class="d-none" id="photo" name="photo" accept="image/*">
                                <label for="photo" class="dropzone p-4 border border-dashed rounded d-block">
                                    <p class="mb-0">Drop files here or click to upload</p>
                                </label>
                            </div>

                         
                            <div id="photoPreview" class="text-center mb-3" style="display:none;">
                                <img id="selectedPhoto" src="<?= $property['ufCrm13PhotoLinks'][0] ?>" class="img-fluid rounded" style="max-height: 300px;" alt="Selected Photo" />
                            </div>

                            <p class="text-muted">Maximum allowed file size: 2MB</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="videoUrl" class="form-label">Video Tour URL</label>
                                    <input type="url" value="<?= $property['ufCrm13VideoTourUrl'] ?>" id="videoUrl" name="videoUrl" class="form-control" placeholder="Enter video tour URL">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="viewUrl" class="form-label">360 View URL</label>
                                    <input type="url" value="<?= $property['ufCrm_42_360_VIEW_URL'] ?>" id="viewUrl" name="viewUrl" class="form-control" placeholder="Enter 360 view URL">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="qrCode" class="form-label">QR Code (Property Booster)</label>
                                    <input type="url" value="<?= $property['ufCrm13QrCodePropertyBooster'] ?>" id="qrCode" name="qrCode" class="form-control" placeholder="Enter QR code URL">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container mt-5 p-3">
                        <div class="row p-3 bg-light rounded border">
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-4">Property Finder Location</h4>
                                <div class="mb-3">
                                    <label for="propertyLocation" class="form-label">Location</label>
                                    <input type="text" value="<?= $property['ufCrm13Location'] ?>" name="propertyLocation" id="propertyLocation" class="form-control">
                                    <!-- <select id="propertyLocation" name="propertyLocation" class="form-select">
								<option value="">Please select</option>
								<option value="1">Location 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="propertyCity" class="form-label">City</label>
                                    <input type="text" value="<?= $property['ufCrm13City'] ?>" name="propertyCity" id="propertyCity" class="form-control">
                                    <!-- <select id="propertyCity" name="propertyCity" class="form-select">
								<option value="">Please select</option>
								<option value="1">City 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="propertyCommunity" class="form-label">Community</label>
                                    <input type="text" value="<?= $property['ufCrm13Community'] ?>" name="propertyCommunity" id="propertyCommunity" class="form-control">
                                    <!-- <select id="propertyCommunity" name="propertyCommunity" class="form-select">
								<option value="">Please select</option>
								<option value="1">Community 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="propertySubCommunity" class="form-label">Sub Community</label>
                                    <input type="text" value="<?= $property['ufCrm13SubCommunity'] ?>" name="propertySubCommunity" id="propertySubCommunity" class="form-control">
                                    <!-- <select id="propertySubCommunity" name="propertySubCommunity" class="form-select">
								<option value="">Please select</option>
								<option value="1">Sub Community 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="propertyTower" class="form-label">Tower/ Build</label>
                                    <input type="text" value="<?= $property['ufCrm13Tower'] ?>" name="propertyTower" id="propertyTower" class="form-control">
                                    <!-- <select id="propertyTower" name="propertyTower" class="form-select">
								<option value="">Please select</option>
								<option value="1">Tower/ Build 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" value="<?= $property['ufCrm13Latitude'] ?>" id="latitude" name="latitude" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" value="<?= $property['ufCrm13Longitude'] ?>" id="longitude" name="longitude" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-4">Bayut Location</h4>
                                <div class="mb-3">
                                    <label for="bayutLocation" class="form-label">Location</label>
                                    <input type="text" value="<?= $property['ufCrm13BayutLocation'] ?>" name="bayutLocation" id="bayutLocation" class="form-control">
                                    <!-- <select id="bayutLocation" name="bayutLocation" class="form-select">
								<option value="">Please select</option>
								<option value="1">Location 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="bayutCity" class="form-label">City</label>
                                    <input type="text" value="<?= $property['ufCrm13BayutCity'] ?>" name="bayutCity" id="bayutCity" class="form-control">
                                    <!-- <select id="bayutCity" name="bayutCity" class="form-select">
								<option value="">Please select</option>
								<option value="1">City 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="bayutCommunity" class="form-label">Community</label>
                                    <input type="text" value="<?= $property['ufCrm13BayutCommunity'] ?>" name="bayutCommunity" id="bayutCommunity" class="form-control">
                                    <!-- <select id="bayutCommunity" name="bayutCommunity" class="form-select">
								<option value="">Please select</option>
								<option value="1">Community 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="bayutSubCommunity" class="form-label">Sub Community</label>
                                    <input type="text" value="<?= $property['ufCrm13BayutSubCommunity'] ?>" name="bayutSubCommunity" id="bayutSubCommunity" class="form-control">
                                    <!-- <select id="bayutSubCommunity" name="bayutSubCommunity" class="form-select">
								<option value="">Please select</option>
								<option value="1">Sub Community 1</option>
							</select> -->
                                </div>
                                <div class="mb-3">
                                    <label for="bayutTower" class="form-label">Tower/ Build</label>
                                    <input type="text" value="<?= $property['ufCrm13BayutTower'] ?>" name="bayutTower" id="bayutTower" class="form-control">
                                    <!-- <select id="bayutTower" name="bayutTower" class="form-select">
								<option value="">Please select</option>
								<option value="1">Tower/ Build 1</option>
							</select> -->
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="container mt-5 p-3">
                        <div class="bg-light p-4 rounded border">
                            <h4 class="mb-4">Floor Plan</h4>
                            <div class="text-center mb-3">
                                <input type="file" name="floorPlan" id="floorPlan" class="d-none" accept="image/*">
                                <label class="dropzone p-4 border border-dashed rounded d-block" for="floorPlan">
                                    <p class="mb-0">Drop files here or click to upload</p>
                                </label>
                            </div>

                            
                            <div id="floorPlanPreview" class="text-center mb-3" style="display:none;">
                                <img src="<?= $property['ufCrm13FloorPlan'][0] ?>" id="selectedFloorPlan" class="img-fluid rounded" style="max-height: 300px;" alt="Selected Floor Plan" />
                            </div>

                            <p class="text-muted">Maximum allowed file size: 2MB</p>
                        </div>
                    </div>

                    <div class="container mt-5 p-3">
                        <!-- <h4 class="mb-4">Add Publishing Information</h4> -->

                        <div class="d-flex gap-3">
                            <!-- Property Finder Block -->
                            <div class="bg-light rounded p-3 flex-fill">
                                <div class="d-flex justify-content-between">
                                    <div class="me-3">
                                        <img src="./assets/pf.png" width="50" height="50" alt="Property Finder" class="me-3">
                                    </div>
                                    <div>
                                        <h5 class="mb-4">Property Finder</h5>
                                        <div>
                                            <input type="checkbox" id="pfEnable" name="pfEnable" class="form-check-input" checked="<?= $property['ufCrm13PfEnable'] ?>">
                                            <label for="pfEnable" class="form-check-label">Enable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bayut Block -->
                            <div class="bg-light rounded p-3 flex-fill">
                                <div class="d-flex justify-content-between">
                                    <div class="me-3">
                                        <img src="./assets/bay.png" width="50" height="50" alt="Bayut" class="d-block mb-2">
                                        <div class="d-flex gap-3">
                                            <div>
                                                <input type="checkbox" id="bayutEnable" name="bayutEnable" class="form-check-input" checked="<?= $property['ufCrm13BayutEnable'] ?>">
                                                <label for="bayutEnable" class="form-check-label">Bayut</label>
                                            </div>

                                            <div>
                                                <input type="checkbox" id="dubizleEnable" name="dubizleEnable" class="form-check-input" checked="<?= $property['ufCrm13DubizleEnable'] ?>">
                                                <label for="dubizleEnable" class="form-check-label">Dubizle</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-4">Bayut</h5>
                                        <div>
                                            <input type="checkbox" id="bayutEnableFull" name="bayutEnableFull" class="form-check-input" checked="<?= $property['ufCrm13BayutEnable'] && $property['ufCrm13DubizleEnable'] ?>">
                                            <label for="bayutEnableFull" class="form-check-label">Enable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Website Block -->
                            <div class="bg-light rounded p-3 flex-fill">
                                <div class="d-flex justify-content-between">
                                    <div class="me-3">
                                        <img src="./assets/web.png" width="50" height="50" alt="Website" class="me-3">
                                    </div>
                                    <div>
                                        <h5 class="mb-4">Website</h5>
                                        <div>
                                            <input type="checkbox" id="websiteEnable" name="websiteEnable" class="form-check-input" checked="<?= $property['ufCrm13WebsiteEnable'] ?>">
                                            <label for="websiteEnable" class="form-check-label">Enable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-3 mb-3">
                        <!-- <button type="button" class="btn btn-success">
					<i class="fa fa-save"></i> Save
				</button> -->
                        <button type="submit" onclick="document.getElementById('mainForm').submit();" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                    <!-- </form> -->
                </div>
            </div>

            <script>
                // Define an array of amenities
                const amenities = [{
                        id: 'gv',
                        label: 'Golf view'
                    },
                    {
                        id: 'cw',
                        label: 'City view'
                    },
                    {
                        id: 'no',
                        label: 'North orientation'
                    },
                    {
                        id: 'so',
                        label: 'South orientation'
                    },
                    {
                        id: 'eo',
                        label: 'East orientation'
                    },
                    {
                        id: 'wo',
                        label: 'West orientation'
                    },
                    {
                        id: 'ns',
                        label: 'Near school'
                    },
                    {
                        id: 'ho',
                        label: 'Near hospital'
                    },
                    {
                        id: 'tr',
                        label: 'Terrace'
                    },
                    {
                        id: 'nm',
                        label: 'Near mosque'
                    },
                    {
                        id: 'sm',
                        label: 'Near supermarket'
                    },
                    {
                        id: 'ml',
                        label: 'Near mall'
                    },
                    {
                        id: 'pt',
                        label: 'Near public transportation'
                    },
                    {
                        id: 'mo',
                        label: 'Near metro'
                    },
                    {
                        id: 'vt',
                        label: 'Near veterinary'
                    },
                    {
                        id: 'bc',
                        label: 'Beach access'
                    },
                    {
                        id: 'pk',
                        label: 'Public parks'
                    },
                    {
                        id: 'rt',
                        label: 'Near restaurants'
                    },
                    {
                        id: 'ng',
                        label: 'Near Golf'
                    },
                    {
                        id: 'ap',
                        label: 'Near airport'
                    },
                    {
                        id: 'cs',
                        label: 'Concierge Service'
                    },
                    {
                        id: 'ss',
                        label: 'Spa'
                    },
                    {
                        id: 'sy',
                        label: 'Shared Gym'
                    },
                    {
                        id: 'ms',
                        label: 'Maid Service'
                    },
                    {
                        id: 'wc',
                        label: 'Walk-in Closet'
                    },
                    {
                        id: 'ht',
                        label: 'Heating'
                    },
                    {
                        id: 'gf',
                        label: 'Ground floor'
                    },
                    {
                        id: 'sv',
                        label: 'Server room'
                    },
                    {
                        id: 'dn',
                        label: 'Pantry'
                    },
                    {
                        id: 'ra',
                        label: 'Reception area'
                    },
                    {
                        id: 'vp',
                        label: 'Visitors parking'
                    },
                    {
                        id: 'op',
                        label: 'Office partitions'
                    },
                    {
                        id: 'sh',
                        label: 'Core and Shell'
                    },
                    {
                        id: 'cd',
                        label: 'Children daycare'
                    },
                    {
                        id: 'cl',
                        label: 'Cleaning services'
                    },
                    {
                        id: 'nh',
                        label: 'Near Hotel'
                    },
                    {
                        id: 'cr',
                        label: 'Conference room'
                    },
                    {
                        id: 'bl',
                        label: 'View of Landmark'
                    },
                    {
                        id: 'pr',
                        label: 'Children Play Area'
                    },
                    {
                        id: 'bh',
                        label: 'Beach Access'
                    }
                ];


                // Function to generate the amenities HTML
                function generateAmenities(amenities) {
                    return amenities.map(amenity => `
			<div class="col-12 form-check">
				<input type="checkbox" class="form-check-input hidden-checkbox" id="${amenity.id}" name="amenities[]" value="${amenity.label}">
				<label class="form-check-label styled-label" for="${amenity.id}">${amenity.label}</label>
			</div>
		`).join('');

                }

                // Insert the amenities into the container
                document.getElementById('amenities-container').innerHTML = generateAmenities(amenities);
            </script>

            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
            <script src="./js/script.js"></script>
            <script>
                document.getElementById('saveAmenities').addEventListener('click', function() {
                    let selectedAmenities = [];

                    // Get all checkboxes
                    const checkboxes = document.querySelectorAll('#amenitiesModal input[type="checkbox"]');

                    // Loop through checkboxes and find the ones that are checked
                    checkboxes.forEach(function(checkbox) {
                        if (checkbox.checked) {
                            // Get the label text corresponding to the checkbox
                            const label = document.querySelector(`label[for=${checkbox.id}]`).innerText;
                            selectedAmenities.push(label);
                        }
                    });

                    // If amenities are selected, hide the button and show the selected amenities
                    if (selectedAmenities.length > 0) {
                        // Hide the Update Amenities button
                        document.querySelector('button[data-bs-target="#amenitiesModal"]').style.display = 'none';

                        // Show the selected amenities
                        const selectedAmenitiesDiv = document.getElementById('selectedAmenities');
                        selectedAmenitiesDiv.innerHTML = `<p class="h6 mb-2">Selected Amenities:</p><ul class="list-unstyled"></ul>`;
                        selectedAmenities.forEach(function(amenity) {
                            selectedAmenitiesDiv.querySelector('ul').innerHTML += `<li>${amenity}</li>`;
                        });
                    }

                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('amenitiesModal'));
                    modal.hide();
                });

                document.getElementById("bayutEnableFull").addEventListener("change", function() {
                    if (this.checked) {
                        document.getElementById("bayutEnable").checked = true;
                        document.getElementById("dubizleEnable").checked = true;
                    }
                });


                // JavaScript to handle image preview
                const floorPlanInput = document.getElementById('floorPlan');
                const floorPlanPreview = document.getElementById('floorPlanPreview');
                const selectedFloorPlan = document.getElementById('selectedFloorPlan');

                const photoInput = document.getElementById('photo');
                const photoPreview = document.getElementById('photoPreview');
                const selectedPhoto = document.getElementById('selectedPhoto');

                floorPlanInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    if (file && file.type.startsWith('image/') && file.size <= 2 * 1024 * 1024) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            selectedFloorPlan.src = e.target.result; // Set the image src
                            floorPlanPreview.style.display = 'block'; // Show the preview
                        };

                        reader.readAsDataURL(file); // Read the file as a data URL
                    } else {
                        // floorPlanPreview.style.display = 'none'; // Hide the preview if invalid
                        alert('Please select a valid image file (Max size: 2MB)');
                    }
                });

                photoInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    if (file && file.type.startsWith('image/') && file.size <= 2 * 1024 * 1024) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            selectedPhoto.src = e.target.result; // Set the image src
                            photoPreview.style.display = 'block'; // Show the preview
                        };

                        reader.readAsDataURL(file); // Read the file as a data URL
                    } else {
                        // photoPreview.style.display = 'none'; // Hide the preview if invalid
                        alert('Please select a valid image file (Max size: 2MB)');
                    }
                });
            </script>


    </form>
</body>

</html>