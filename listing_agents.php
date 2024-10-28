<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve the properties from the session
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

function deleteLandlord($agentId)
{
    CRest::call('crm.item.delete', [
        'entityTypeId' => LISTING_AGENTS_ENTITY_TYPE_ID,
        'id' => $agentId
    ]);

    header('Location: landlords.php');
}

$result = CRest::call('crm.item.list', [
    'entityTypeId' => LISTING_AGENTS_ENTITY_TYPE_ID
]);

$listing_agents = $result['result']['items'] ?? [];

$data = $_POST;

if ($data) {
    $response = CRest::call('crm.item.add', [
        'entityTypeId' => LISTING_AGENTS_ENTITY_TYPE_ID,
        'fields' => [
            'ufCrm14AgentName' => $data['name'],
            'ufCrm14AgentEmail' => $data['email'],
            'ufCrm14AgentMobile' => $data['mobile'],
            'ufCrm14AgentDesignation' => $data['designation'],
            'ufCrm14AgentRole' => $data['role'],
        ]
    ]);

    header('Location: listing_agents.php');
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
        label {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 10px;
            border-radius: 10px;
        }

        @media (max-width: 767.98px) {
            #sidebar {
                position: fixed;
                left: -250px;
                transition: left 0.3s ease-in-out;
                z-index: 1040;
            }

            #sidebar.active {
                left: 0;
            }

            #sidebarToggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            #sidebar.active~#sidebarToggle {
                left: 270px;
            }

            #sidebarClose {
                display: none;
            }

            #sidebar.active #sidebarClose {
                display: block;
            }
        }

        @media (min-width: 768px) {

            #sidebarToggle,
            #sidebarClose {
                display: none;
            }
        }

        #sidebar .nav-link {
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background-color: #f8f9fa;
            color: #007bff;
        }

        #sidebar .nav-link i {
            width: 20px;
            text-align: center;
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

            <!-- Listing Agents Content -->
            <div class="container px-3 px-md-5 py-2 py-md-4">
                <h2 class="display-10 fw-bold text-primary container">Listing Agents</h2>
                <div class="custom-card container mt-4">
                    <!-- Add Location Button -->
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addLandlordModal">
                            <i class="fas fa-plus me-2"></i>Add Listing Agent
                        </button>
                    </div>

                    <!-- Listing Agents Table -->
                    <div class="table-responsive">
                        <table class="table table-borderless text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Designation</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="locationTableBody">
                                <?php foreach ($listing_agents as $listing_agent) : ?>
                                    <tr>
                                        <td><?= $listing_agent['id'] ?></td>
                                        <td><?= $listing_agent['ufCrm14AgentName'] ?></td>
                                        <td><?= $listing_agent['ufCrm14AgentEmail'] ?></td>
                                        <td><?= $listing_agent['ufCrm14AgentMobile'] ?></td>
                                        <td><?= $listing_agent['ufCrm14AgentDesignation'] ?></td>
                                        <td><?= $listing_agent['ufCrm14AgentRole'] ?></td>
                                        <td>
                                            <form action="./delete_agent.php" method="POST">
                                                <input type="hidden" name="agentId" value="<?= $listing_agent['id'] ?>">
                                                <input type="hidden" name="entityTypeId" value="<?= LISTING_AGENTS_ENTITY_TYPE_ID ?>">
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

            <!-- Modal for Adding Listing Agent -->
            <div class="modal fade" id="addLandlordModal" tabindex="-1" aria-labelledby="addLandlordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLandlordModalLabel">Add Listing Agent</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addLandlordForm" method="post" action="./listing_agents.php">
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
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select name="role" id="role" class="form-control">
                                        <option value="Agent">Agent</option>
                                        <option value="Team Lead">Team Lead</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" form="addLandlordForm">Add Listing Agent</button>
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