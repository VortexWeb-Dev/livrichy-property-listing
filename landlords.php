<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve session data
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

function deleteLandlord($landlordId)
{
    CRest::call('crm.item.delete', [
        'entityTypeId' => LANDLORDS_ENTITY_TYPE_ID,
        'id' => $landlordId
    ]);

    header('Location: landlords.php');
}

$result = CRest::call('crm.item.list', [
    'entityTypeId' => LANDLORDS_ENTITY_TYPE_ID
]);

$landlords = $result['result']['items'] ?? [];

$data = $_POST;

if ($data) {
    $response = CRest::call('crm.item.add', [
        'entityTypeId' => LANDLORDS_ENTITY_TYPE_ID,
        'fields' => [
            'ufCrm17LandlordName' => $data['name'],
            'ufCrm17LandlordEmail' => $data['email'],
            'ufCrm17LandlordMobile' => $data['mobile'],
        ]
    ]);

    header('Location: landlords.php');
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

            <div class="container px-3 px-md-5 py-2 py-md-4">
                <h2 class="display-10 fw-bold text-primary container">Landlords</h2>
                <div class="custom-card container mt-4">
                    <!-- Add Location Button -->
                    <div class="text-end">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addLandlordModal">
                            Add Landlord
                        </button>
                    </div>
                    <!-- landlords Table -->
                    <div class="table-responsive">
                        <table class="table table-borderless text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="locationTableBody">
                                <?php foreach ($landlords as $landlord) : ?>
                                    <tr>
                                        <td><?= $landlord['id'] ?></td>
                                        <td><?= $landlord['ufCrm17LandlordName'] ?></td>
                                        <td><?= $landlord['ufCrm17LandlordEmail'] ?></td>
                                        <td><?= $landlord['ufCrm17LandlordMobile'] ?></td>
                                        <td>
                                            <form action="./delete_landlord.php" method="POST">
                                                <input type="hidden" name="landlordId" value="<?= $landlord['id'] ?>">
                                                <input type="hidden" name="entityTypeId" value="<?= LANDLORDS_ENTITY_TYPE_ID ?>">
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
        </div>

        <!-- Modal for Adding Location -->
        <div class="modal fade" id="addLandlordModal" tabindex="-1" aria-labelledby="addLandlordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLandlordModalLabel">Add Landlord</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addLandlordForm" method="post" action="./landlords.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="addLandlordForm">Add Landlord</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>
</body>

</html>