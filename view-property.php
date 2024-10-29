<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve session data
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

$response = CRest::call('crm.item.get', [
    'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
    'id' => $_GET['id']
]);

$property = $response['result']['item'] ?? null;
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* background-color: #f3f4f6; */
        }

        h2.display-10 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .property-tag {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 1rem;
            background-color: #007bff;
            color: white;
        }

        .property-tag.bg-secondary {
            background-color: #6c757d;
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 0.9rem;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
        }

        .card-body {
            padding: 1rem;
        }

        table.table-sm {
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        table.table-sm td {
            padding: 0.5rem 0;
            color: #495057;
        }

        .btn-primary,
        .badge.bg-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .listing-images img {
            border-radius: 0.75rem;
            transition: transform 0.3s ease;
        }

        .listing-images img:hover {
            transform: scale(1.05);
        }

        .amenity-badge {
            border: 1px solid #007bff;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            display: inline-block;
            font-size: 0.85rem;
            background-color: rgba(0, 123, 255, 0.1);
        }

        .section-bg {
            padding: 2rem 0;
            margin-bottom: 1rem;
            background-color: #f1f1f1;
            border-radius: 0.5rem;
        }

        h3.h3 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* image gallery  */
        .gallery-preview img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .carousel-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }

        .carousel {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-item {
            flex: 0 0 200px;
            height: 150px;
            margin-right: 10px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #333;
        }

        .carousel-item img {
            height: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
            border-radius: 5px;
        }

        .carousel-item img.active {
            opacity: 1;
            border: 2px solid #007bff;
        }

        .prev-button,
        .next-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .prev-button {
            left: 10px;
        }

        .next-button {
            right: 10px;
        }

        /* Mobile responsiveness improvements */
        @media (max-width: 768px) {
            .text-md-end {
                text-align: left !important;
            }

            .card-body h5.card-title {
                font-size: 1rem;
            }

            .listing-images img {
                height: 150px;
            }
        }
    </style>
</head>

<body>

    <div class="d-flex">
        <!-- Sticky Left Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <!-- Fixed Topbar -->
            <?php include 'includes/topbar.php'; ?>

            <div class="container px-3 px-md-5 py-2 py-md-4">
                <h2 class="display-10 fw-bold text-primary">Property Details</h2>
                <div class="custom-card">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h1 class="h3"><?php echo $property['title'] ?></h1>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <h2 class="h3">AED <?php echo $property['ufCrm13Price'] ?></h2>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> <?php echo $property['ufCrm13City'] . ' - ' . $property['ufCrm13Community'] . ' - ' . $property['ufCrm13SubCommunity'] . ' - ' . $property['ufCrm13Tower'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3 d-flex flex-wrap gap-1">
                                <span class="badge bg-primary property-tag"><?php echo $property['ufCrm13PropertyType'] == 'AP' ? 'Apartment' : $property['ufCrm13PropertyType'] ?></span>
                                <span class="badge bg-secondary property-tag">Beds: <?php echo $property['ufCrm13Bedroom'] ?></span>
                                <span class="badge bg-secondary property-tag">Baths: <?php echo $property['ufCrm13Bathroom'] ?></span>
                                <span class="badge bg-secondary property-tag">Sq Ft: <?php echo $property['ufCrm13Size'] ?></span>
                            </div>

                            <h2>Description</h2>
                            <?php echo $property['ufCrm13DescriptionEn'] ?>

                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Listed By</h5>
                                    <span><?php echo $property['ufCrm13AgentName'] ?></span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Property Details</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Property ID:</td>
                                            <td><?php echo isset($property['ufCrm13ReferenceId']) ? $property['ufCrm13ReferenceId'] : 'Unavailable'; ?></td>
                                        </tr>

                                        <tr>
                                            <td>Price:</td>
                                            <td>AED <?php echo $property['ufCrm13Price'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Bedrooms:</td>
                                            <td><?php echo $property['ufCrm13Bedroom'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Bathrooms:</td>
                                            <td><?php echo $property['ufCrm13Bathroom'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Parking:</td>
                                            <td><?php echo $property['ufCrm13Parking'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Property Size:</td>
                                            <td><?php echo $property['ufCrm13Size'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Property Type:</td>
                                            <td><?php echo $property['ufCrm13PropertyType'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Offering Type:</td>
                                            <td><?php echo $property['ufCrm13OfferingType'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Property Status:</td>
                                            <td><?php echo $property['ufCrm13Status'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Image gallery -->
                    <!-- <div class="row mt-3 w-100 listing-images">
                        <?php for ($i = 0; $i < count($property['ufCrm13PhotoLinks']); $i++) { ?>
                            <div class="col-6 col-md-3 mb-3">
                                <img src="<?= htmlspecialchars($property['ufCrm13PhotoLinks'][$i]) ?>" alt="Image <?= $i + 1 ?>" class="img-fluid rounded">
                            </div>
                        <?php } ?>
                    </div> -->

                    <!-- Image Preview -->
                    <div class="gallery-preview mb-4">
                        <img src="<?= htmlspecialchars($property['ufCrm13PhotoLinks'][0]) ?>" alt="Preview" id="previewImage">
                    </div>

                    <!-- Thumbnails Carousel -->
                    <div class="carousel-container">
                        <div class="carousel">
                            <!-- Carousel items will be dynamically added here -->
                            <?php for ($i = 0; $i < count($property['ufCrm13PhotoLinks']); $i++) { ?>
                                <div class="carousel-item">
                                    <img src="<?= htmlspecialchars($property['ufCrm13PhotoLinks'][$i]) ?>" alt="image - .<?= $i + 1 ?>" data-src="<?= htmlspecialchars($property['ufCrm13PhotoLinks'][$i]) ?>" class="thumbnail">
                                </div>
                            <?php } ?>
                        </div>
                        <button class="prev-button">&#10094;</button>
                        <button class="next-button">&#10095;</button>
                    </div>


                    <!-- Private Aminities -->
                    <!-- <section class="section-bg">
                        <div class="row">
                            <div class="col-12">
                                <h2 class="mb-4">Private Amenities</h2>
                                <?php if (!empty($property['ufCrm13Amenities']) && is_array($property['ufCrm13Amenities'])): ?>
                                    <div>
                                        <?php foreach (explode(',', $property['ufCrm13Amenities'][0]) as $amenity): ?>
                                            <span class="amenity-badge"><?php echo htmlspecialchars($amenity); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No amenities available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section> -->

                    <!-- Floor plans -->
                    <!-- <section class="section-bg">
                        <div class="row">
                            <div class="col-12">
                                <h2 class="mb-4">Floor plans</h2>

                                <div class="bg-light p-5 text-center">
                                    <a href="<?= htmlspecialchars($property['ufCrm13FloorPlan'][0]['urlMachine']) ?>">Download floor plan</a>
                                </div>
                            </div>
                        </div>
                    </section> -->
                </div>
            </div>
        </div>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>

    </form>
    <script>
        // JavaScript to handle thumbnail click and change the preview image

        const carousel = document.querySelector('.carousel');
        const prevButton = document.querySelector('.prev-button');
        const nextButton = document.querySelector('.next-button');

        const totalItems = 10;
        const visibleItems = 4;
        let currentIndex = 0;

        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentIndex * 210}px)`;
        }

        function moveNext() {
            currentIndex++;
            if (currentIndex >= totalItems) {
                currentIndex = 0;
                carousel.style.transition = 'none';
                updateCarousel();
                setTimeout(() => {
                    carousel.style.transition = 'transform 0.5s ease';
                }, 10);
            } else {
                updateCarousel();
            }
        }

        function movePrev() {
            currentIndex--;
            if (currentIndex < 0) {
                currentIndex = totalItems - 1;
                carousel.style.transition = 'none';
                updateCarousel();
                setTimeout(() => {
                    carousel.style.transition = 'transform 0.5s ease';
                }, 10);
            } else {
                updateCarousel();
            }
        }

        nextButton.addEventListener('click', moveNext);
        prevButton.addEventListener('click', movePrev);

        // Clone necessary items for seamless looping
        const itemWidth = 210; // 200px width + 10px margin
        const cloneCount = Math.ceil(carousel.offsetWidth / itemWidth);

        for (let i = 0; i < cloneCount; i++) {
            const clone = carousel.children[i].cloneNode(true);
            carousel.appendChild(clone);
        }

        // Initial position
        updateCarousel();

        //update the thumbnails on click
        const thumbnails = document.querySelectorAll('.thumbnail');
        const previewImage = document.getElementById('previewImage');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                previewImage.src = this.getAttribute('data-src');

                thumbnails.forEach(img => img.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>