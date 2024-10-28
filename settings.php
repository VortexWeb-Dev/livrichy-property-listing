<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve session data
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';

    $data = $_POST;
    $fields = [
        'ufCrm19ListingReference' => $data['listingReference'],
        'ufCrm19Website' => $data['website'],
        'ufCrm19CompanyName' => $data['companyName'],
        'ufCrm19RentOrSale' => $data['rentOrSale'] == 'Rent' ? 45268 : 45270,
        'ufCrm19Watermark' => $data['watermark'] ? 'Y' : 'N',
        'ufCrm19AgentIdInReference' => $data['agentIdInReference'] ? 'Y' : 'N',
        'ufCrm19AgentCanEditLiveListings' => $data['agentCanEditLiveListing'] ? 'Y' : 'N',
        'ufCrm19AgentCanEditPendingListings' => $data['agentCanEditPendingListing'] ? 'Y' : 'N',
        'ufCrm19EmailNotification' => $data['emailNotification'] ? 'Y' : 'N',
    ];

    CRest::call('crm.item.update', [
        'entityTypeId' => GENERAL_SETTINGS_ENTITY_TYPE_ID,
        'id' => 2,
        'fields' => $fields
    ]);

    // if (isset($_FILES['watermarkImage']) && $_FILES['watermarkImage']['error'] === UPLOAD_ERR_OK) {
    //     $uploadDir = __DIR__ . '/../assets/';
    //     $uploadFile = $uploadDir . 'watermark.png';

    //     // Check if the file is a valid image
    //     $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
    //     $fileType = mime_content_type($_FILES['watermarkImage']['tmp_name']);

    //     if (in_array($fileType, $allowedTypes)) {
    //         // Move the uploaded file to the assets directory
    //         if (move_uploaded_file($_FILES['watermarkImage']['tmp_name'], $uploadFile)) {
    //             echo "File uploaded successfully.";
    //         } else {
    //             echo "Error moving the uploaded file.";
    //         }
    //     } else {
    //         echo "Invalid file type. Please upload a valid image (PNG, JPEG, GIF).";
    //     }
    // }

    header('Location: settings.php');
}

$result = CRest::call('crm.item.get', [
    'entityTypeId' => GENERAL_SETTINGS_ENTITY_TYPE_ID,
    'id' => 2
]);
$settings = $result['result']['item'] ?? [];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="/styles/app.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .btn-custom {
            background-color: #007bff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 1.1rem;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            color: #fff;
            background-color: #0056b3;
        }

        input[type="file"] {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
        }

        .watermark-card {
            background-color: #fff;
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .watermark-card .card-body {
            padding: 1.5rem;
        }

        label {
            font-weight: 600;
        }

        /* Customize the file upload button */
        .file-upload-wrapper {
            position: relative;
        }

        .file-upload {
            width: 100%;
            height: 50px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            border-radius: 0.5rem;
        }

        .file-upload input[type="file"] {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
        }

        .file-upload label {
            padding: 15px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            cursor: pointer;
            display: block;
            transition: all 0.3s;
        }

        .file-upload label:hover {
            background-color: #0056b3;
        }

        .property-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
            /* Adjust this as needed */
        }

        .property-image {
            width: 100%;
            height: auto;
            border-radius: 0.75rem;
        }

        .watermark-overlay {
            position: absolute;
            bottom: 0px;
            right: 0px;
            opacity: 0.3;
            width: 100%;
            /* Adjust watermark size */
            height: 100%;
            z-index: 2;
        }

        @media (max-width: 768px) {
            .custom-card {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <!-- Fixed Topbar -->
            <?php include 'includes/topbar.php'; ?>

            <!-- Settings Content -->
            <div class="container px-3 px-md-5 py-2 py-md-4">
                <h2 class="display-6 fw-bold text-primary mb-4">Settings</h2>
                <div class="main-content">
                    <form method="POST" enctype="multipart/form-data" action="./settings.php">
                        <div class="row">
                            <div class="col-md-6 d-flex justify-content-center align-items-center">
                                <div class="card shadow watermark-card mb-4">
                                    <div class="card-body d-flex flex-column align-items-center gap-3">
                                        <h5 class="card-title mb-3">Edit Watermark Image</h5>
                                        <!-- Property Image with Watermark Overlay -->
                                        <div class="property-wrapper">
                                            <img src="./assets/property.jpg?<?php echo time(); ?>" alt="Property" class="property-image img-fluid rounded">
                                            <img src="./assets/watermark.png?<?php echo time(); ?>" alt="Watermark" class="watermark-overlay">
                                        </div>
                                        <div class="file-upload-wrapper w-100">
                                            <div class="file-upload">
                                                <input type="file" name="watermarkImage" class="form-control-file">
                                                <label>Upload Watermark</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card watermark-card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-2 fw-light">General Settings</h5>
                                        <div class="mb-3">
                                            <label for="listingReference" class="form-label fw-light">Listing Reference</label>
                                            <input type="text" class="form-control" id="listingReference" name="listingReference" value="<?= $settings['ufCrm19ListingReference'] ?? '' ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="website" class="form-label fw-light">Website</label>
                                            <input type="text" class="form-control" id="website" name="website" value="<?= $settings['ufCrm19Website'] ?? '' ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="companyName" class="form-label fw-light">Company Name</label>
                                            <input type="text" class="form-control" id="companyName" name="companyName" value="<?= $settings['ufCrm19CompanyName'] ?? '' ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-light">Rent or Sale</label>
                                            <select class="form-select mb-1" name="rentOrSale">
                                                <option selected><?= $settings['ufCrm19RentOrSale'] == 45268 ? 'Rent' : 'Sale' ?? '' ?></option>
                                                <option value="45268">Rent</option>
                                                <option value="45270">Sale</option>
                                            </select>
                                        </div>

                                        <!-- Switches -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="watermark" id="watermark" <?= $settings['ufCrm19Watermark'] == 'Y' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-light" for="watermark">Watermark</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="agentIdInReference" id="agentIdInReference" <?= $settings['ufCrm19AgentIdInReference'] == 'Y' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-light" for="agentIdInReference">Agent ID in Reference</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="agentCanEditLiveListing" id="agentCanEditLiveListing" <?= $settings['ufCrm19AgentCanEditLiveListings'] == 'Y' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-light" for="agentCanEditLiveListing">Agent Can Edit Live Listings</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="agentCanEditPendingListing" id="agentCanEditPendingListings" <?= $settings['ufCrm19AgentCanEditPendingListings'] == 'Y' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-light" for="agentCanEditPendingListings">Agent Can Edit Pending Listings</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="emailNotification" id="emailNotification" <?= $settings['ufCrm19EmailNotification'] == 'Y' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-light" for="emailNotification">Email Notification</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-custom mt-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.add('active');
                sidebarToggle.style.left = '270px';
            });

            sidebarClose.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarToggle.style.left = '20px';
            });

            // Close sidebar when clicking outside of it
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggleButton = sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggleButton && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarToggle.style.left = '20px';
                }
            });

            // Add active class to current page link
            const currentPage = window.location.pathname.split("/").pop();
            const navLinks = document.querySelectorAll('#sidebar .nav-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href').includes(currentPage)) {
                    link.classList.add('active');
                }
            });
        });

        function onScreenResize() {
            // Get the screen width
            var screenWidth = window.innerWidth;

            // Add or remove the class based on screen size
            if (screenWidth > 768) {
                document.querySelector('.main-content').classList.add('custom-card');
            } else {
                document.querySelector('.main-content').classList.remove('custom-card');
            }

            // Update the class on window resize
            window.addEventListener('resize', function() {
                var screenWidth = window.innerWidth;
                if (screenWidth > 768) {
                    document.querySelector('.main-content').classList.add('custom-card');
                } else {
                    document.querySelector('.main-content').classList.remove('custom-card');
                }
            });
        }

        onScreenResize();
    </script>
</body>

</html>