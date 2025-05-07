<div class="bg-white shadow-md rounded-lg p-6 ">
    <h2 class="text-2xl font-semibold">Marketing Image</h2>
    <p class="text-sm text-gray-500 mb-4">Please fill in all the required fields</p>

    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : null;

    if ($page === 'edit-property') {
        include_once('views/components/edit-listing-marketing.php');
    } else {
        include_once('views/components/create-listing-marketing.php');
    } ?>

</div>