<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve the properties from the session
session_start();
if (isset($_SESSION["properties"])) {
    $properties = $_SESSION["properties"];
}

function parseLocation($location)
{
    $parts = explode(' - ', $location);

    if (count($parts) !== 4) {
        return "Invalid input format. Expected format: City - Community - Sub Community - Building";
    }

    return array(
        'location' => $location,
        'city' => $parts[0],
        'community' => $parts[1],
        'sub_community' => $parts[2],
        'building' => $parts[3],
    );
}

function deleteLocation($locationId)
{
    CRest::call('crm.item.delete', [
        'entityTypeId' => LOCATIONS_ENTITY_TYPE_ID,
        'id' => $locationId
    ]);

    header('Location: locations.php');
}

$result = CRest::call('crm.item.list', [
    'entityTypeId' => LOCATIONS_ENTITY_TYPE_ID
]);

$locations = $result['result']['items'] ?? [];

$data = $_POST;

if ($data) {
    $location = parseLocation($data['location']);

    $response = CRest::call('crm.item.add', [
        'entityTypeId' => LOCATIONS_ENTITY_TYPE_ID,
        'fields' => [
            'ufCrm15Location' => $data['location'],
            'ufCrm15City' => $location['city'],
            'ufCrm15Community' => $location['community'],
            'ufCrm15SubCommunity' => $location['sub_community'],
            'ufCrm15Building' => $location['building'],
        ]
    ]);

    header('Location: locations.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing - Locations</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="/styles/app.css">

    <!-- Custom CSS -->
    <style>
        .dropdown-menu.show {
            display: block;
        }

        label {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 10px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sticky Left Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Location Table and Modal -->
        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <!-- Fixed Topbar -->
            <?php include 'includes/topbar.php'; ?>

            <!-- Main content -->
            <div class="container px-3 px-md-5 py-2 py-md-4">
                <h2 class="display-10 fw-bold text-primary container">Locations</h2>
                <div class="custom-card container mt-4">
                    <!-- Add Location Button -->
                    <div class="text-end">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                            Add Location
                        </button>
                    </div>
                    <!-- Locations Table -->
                    <div class="table-responsive">
                        <table class="table table-borderless text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="locationTableBody">
                                <?php foreach ($locations as $location) : ?>
                                    <tr>
                                        <td><?= $location['id'] ?></td>
                                        <td><?= $location['ufCrm15Location'] ?></td>
                                        <td>
                                            <form action="./delete_location.php" method="POST">
                                                <input type="hidden" name="locationId" value="<?= $location['id'] ?>">
                                                <input type="hidden" name="entityTypeId" value="<?= LOCATIONS_ENTITY_TYPE_ID ?>">
                                                <button class="btn btn-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal for Adding Location -->
            <div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLocationModalLabel">Add Location</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addLocationForm" method="post" action="./locations.php">
                                <div class="mb-3">
                                    <label for="locationInput" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="locationInput" name="location" placeholder="City - Community - Sub Community - Building" required>
                                </div>
                            </form>
                            <p>Enter the location in the following format:<br>"City - Community - Sub Community - Building"</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" form="addLocationForm">Add Location</button>
                        </div>
                    </div>
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
            const mainContent = document.querySelector('.flex-grow-1');

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
    </script>
</body>

</html>