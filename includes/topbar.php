<!-- Fixed Topbar -->
<?php
$availability = $result['total'];
$listings = $result['total'];

?>
<div class="bg-white shadow-sm" style="position: sticky; top: 0; z-index: 1000;">
	<div class="container-fluid" style="height: 60px;">
		<div class="row align-items-center h-100">
			<div class="col-12 col-md-6 h-100 d-flex align-items-center d-flex items-center">
				<!-- Toggle button for sidebar -->
				<button id="sidebarToggle" class="me-4 btn btn-primary d-md-none rounded-circle" style="top: 20px; left: 20px; z-index: 1050; width: 50px; height: 50px;">
					<i class="fa-solid fa-bars"></i>
				</button>
				<!-- <div class="me-4">
					<span class="fs-6 fw-light me-2">Availability:</span>
					<span class="badge bg-success rounded-pill">
						<?= $availability ?>
					</span>
				</div> -->
				<div>
					<span class="fs-6 fw-light me-2">Listings:</span>
					<span class="badge bg-primary rounded-pill">
						<?= $listings ?>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>