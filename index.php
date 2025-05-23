<?php
require __DIR__ . '/crest/settings.php';
require __DIR__ . '/controllers/SpaController.php';
require __DIR__ . '/utils/index.php';

include __DIR__ . '/views/header.php';
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtersStr = sessionStorage.getItem('filters');
        if (filtersStr) {
            try {
                document.querySelector('#clearFiltersBtn').classList.remove('d-none');
            } catch (e) {
                // If JSON parsing fails, show the clear button just in case
                document.querySelector('#clearFiltersBtn').classList.remove('d-none');
            }
        }
    });
</script>

<?php

$currentUser = fetchCurrentUser();
$currentUserId = $currentUser['ID'];
$isAdmin = isAdmin($currentUserId);

echo '<script>console.log("Welcome, " + ' . json_encode($currentUser['NAME']) . ')</script>';

include 'views/components/toast.php';
include 'views/components/topbar.php';

$pages = [
    'properties' => 'views/properties/index.php',
    'add-property' => 'views/properties/add.php',
    'edit-property' => 'views/properties/edit.php',
    'view-property' => 'views/properties/view.php',

    'agents' => 'views/agents/index.php',
    'developers' => 'views/developers/index.php',
    'history' => 'views/history/index.php',
    'pf-locations' => 'views/pf-locations/index.php',
    'bayut-locations' => 'views/bayut-locations/index.php',
    'settings' => 'views/settings/index.php',
    'reports' => 'views/reports/index.php',
];

$page = isset($_GET['page']) && array_key_exists($_GET['page'], $pages) ? $_GET['page'] : 'properties';

require $pages[$page];

if (!array_key_exists($page, $pages)) {
    header("Location: index.php?page=properties';");
    exit;
}
?>

<script>
    localStorage.setItem('isAdmin', <?php echo json_encode($isAdmin); ?>);
</script>

<?php
include __DIR__ . '/views/footer.php';
