<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve the properties from the session
session_start();
if (isset($_SESSION["properties"])) {
    $properties = $_SESSION["properties"];
}

$result = CRest::call('crm.item.list', [
    'entityTypeId' => DEVELOPERS_ENTITY_TYPE_ID
]);

$developers = $result['result']['items'] ?? [];

$data = $_POST;

if ($data) {
    $response = CRest::call('crm.item.add', [
        'entityTypeId' => DEVELOPERS_ENTITY_TYPE_ID,
        'fields' => [
            'ufCrm18DeveloperName' => $data['developer'],
        ]
    ]);

    header('Location: developers.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="/styles/app.css">

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

        @media (max-width: 767.98px) {
            .custom-card {
                margin-left: 0 !important;
                transition: margin-left 0.3s ease-in-out;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Fixed Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <!-- Fixed Topbar -->
            <?php include 'includes/topbar.php'; ?>

            <div class="container px-3 px-md-5 py-2 py-md-4">
                <h2 class="display-10 fw-bold text-primary container">Developers</h2>
                <div class="custom-card">
                    <div class="container mt-4">
                        <div class="text-end">
                            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDeveloperModal">
                                Add Developer
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Developer Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="locationTableBody">
                                    <?php foreach ($developers as $developer) : ?>
                                        <tr>
                                            <td><?= $developer['id'] ?></td>
                                            <td><?= $developer['ufCrm18DeveloperName'] ?></td>
                                            <td>
                                                <form action="./delete_developer.php" method="POST">
                                                    <input type="hidden" name="developerId" value="<?= $developer['id'] ?>">
                                                    <input type="hidden" name="entityTypeId" value="<?= DEVELOPERS_ENTITY_TYPE_ID ?>">
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

                <div class="modal fade" id="addDeveloperModal" tabindex="-1" aria-labelledby="addDeveloperModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDeveloperModalLabel">Add Developer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addDeveloperForm" method="post" action="./developers.php">
                                    <div class="mb-3">
                                        <label for="developer" class="form-label">Developer Name</label>
                                        <input type="text" class="form-control" id="developer" name="developer" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" form="addDeveloperForm">Add Developer</button>
                            </div>
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

            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggleButton = sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggleButton && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarToggle.style.left = '20px';
                }
            });

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