<div class="container my-4 flex justify-between">
  <div class="mb-3 mb-lg-0 flex gap-2">
    <div class="flex gap-2 items-center">
      <!-- Filter Dropdown -->
      <div class="relative me-2">
        <?php
        $filterLabels = [
          'ALL' => 'All Listings',
          'DRAFT' => 'Draft',
          'LIVE' => 'Live',
          'PENDING' => 'Pending',
          'ARCHIVED' => 'Archived',
          'DUPLICATE' => 'Duplicate',
        ];
        $currentFilter = $filter ?? 'ALL'; // Default to 'ALL' if no filter is set
        $currentFilterLabel = $filterLabels[$currentFilter] ?? 'Select Filter';
        ?>
        <button class="btn btn-filter btn-outline-primary dropdown-toggle w-full py-2 px-4 rounded-md border border-primary text-primary"
          type="button"
          id="listingFiltersDropdown"
          data-bs-toggle="dropdown"
          aria-expanded="false">
          <?= $currentFilterLabel ?>
        </button>
        <ul class="dropdown-menu absolute w-full mt-2 border border-gray-300 bg-white shadow-md" aria-labelledby="listingFiltersDropdown">
          <?php foreach ($filterLabels as $key => $label): ?>
            <li><button class="dropdown-item filter-item" onclick="filterProperties('<?= $key ?>')"><?= $label ?></button></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
        Filters
      </button>
      
    </div>
  </div>

  <script>
    const savedFilter = localStorage.getItem('listingFilter') || 'ALL';
    document.querySelectorAll('.dropdown-item').forEach(item => {
      if (item.innerText === document.querySelector('.btn').innerText) {
        item.classList.add('active');
      }
    });

    function filterProperties(filterKey) {
      localStorage.setItem('listingFilter', filterKey);

      const filterLabels = {
        'ALL': 'All Listings',
        'DRAFT': 'Draft',
        'LIVE': 'Live',
        'PENDING': 'Pending',
        'ARCHIVED': 'Archived',
        'DUPLICATE': 'Duplicate',
      };

      document.querySelector('.btn.btn-filter').innerText = filterLabels[filterKey] || 'Select Filter';

      document.querySelectorAll('.dropdown-item.filter-item').forEach(item => {
        if (item.innerText === filterLabels[filterKey]) {
          item.classList.add('active');
        } else {
          item.classList.remove('active');
        }
      });

      if (filterKey === 'ALL') {
        fetchProperties(currentPage);
        return;
      }

      fetchProperties(currentPage, {
        'ufCrm13Status': filterKey
      });

    }
  </script>

  <div class="flex flex-wrap justify-end items-center gap-2">
    <!-- XML Publish Dropdown -->
    <a href="?page=add-property" class="btn btn-primary py-2 px-4 rounded-md">Create Listing</a>
    <div class="relative me-2">
      <button class="btn btn-outline-primary dropdown-toggle w-full py-2 px-4 rounded-md border border-primary text-primary"
        type="button"
        id="xmlPublishDropdown"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        XML Publish
      </button>
      <ul class="dropdown-menu absolute w-full mt-2 border border-gray-300 bg-white shadow-md" aria-labelledby="xmlPublishDropdown">
        <li><a class="dropdown-item" href="pf-xml.php">PF</a></li>
        <li><a class="dropdown-item" href="bayut-xml.php">Bayut</a></li>
        <li><a class="dropdown-item" href="dubizzle-xml.php">Dubizzle</a></li>
        <li><a class="dropdown-item" href="website-xml.php">Website</a></li>
      </ul>
    </div>

    <!-- Bulk Actions Dropdown -->
    <div class="relative">
      <button class="btn btn-secondary py-2 px-4 rounded-md bg-secondary text-white dropdown-toggle"
        type="button"
        id="bulkActionsDropdown"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fas fa-cog me-2"></i>Bulk Actions
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-md absolute w-76 mt-2 border border-gray-300 bg-white text-sm" aria-labelledby="bulkActionsDropdown">
        <li>
          <h6 class="dropdown-header">Transfer</h6>
        </li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="selectAndAddPropertiesToAgentTransfer()"><i class="fas fa-user-tie me-2"></i>Transfer to Agent</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="selectAndAddPropertiesToOwnerTransfer()"><i class="fas fa-user me-2"></i>Transfer to Owner</button></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <h6 class="dropdown-header">Publish</h6>
        </li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish')"><i class="fas fa-bullhorn me-2"></i>Publish All</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'pf')"><i class="fas fa-search me-2"></i>Publish To PF</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'bayut')"><i class="fas fa-building me-2"></i>Publish To Bayut</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'dubizzle')"><i class="fas fa-home me-2"></i>Publish To Dubizzle</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'website')"><i class="fas fa-globe me-2"></i>Publish To Website</button></li>
        <li>
          <hr class="dropdown-divider">
        </li>

        <li>
          <h6 class="dropdown-header">Unpublish</h6>
        </li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish')"><i class="fas fa-eye-slash me-2"></i>Unpublish</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'pf')"><i class="fas fa-search me-2"></i>Unpublish from PF</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'bayut')"><i class="fas fa-building me-2"></i>Unpublish from Bayut</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'dubizzle')"><i class="fas fa-home me-2"></i>Unpublish from Dubizzle</button></li>
        <li><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'website')"><i class="fas fa-globe me-2"></i>Unpublish from Website</button></li>
        <li>
          <hr class="dropdown-divider">
        </li>

        <li><button class="dropdown-item text-danger px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('archive')"><i class="fas fa-archive me-2"></i>Archive</button></li>
        <li><button class="dropdown-item text-danger px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('delete')"><i class="fas fa-trash-alt me-2"></i>Delete</button></li>
      </ul>
    </div>


  </div>
</div>