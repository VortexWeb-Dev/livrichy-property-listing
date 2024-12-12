<!-- Add PfLocation Modal -->
<div id="addModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Add PF Location</h3>

        <form id="addPfLocationForm" onsubmit="handleAddLocation(event)">
            <div class="mb-4">
                <label for="location" class="block text-sm font-semibold text-gray-800">Location</label>
                <input type="text" id="location" name="location" required class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:border-blue-500" placeholder="City - Community - Sub Community - Building/Tower">
            </div>

            <div class="flex justify-end space-x-2">
                <button
                    type="button"
                    onclick="toggleModal(false)"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add PF Location
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function addItem(entityTypeId, fields) {
        try {
            const response = await fetch(`https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.add?entityTypeId=${entityTypeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fields,
                }),
            });

            if (response.ok) {
                toggleModal(false);
                location.reload();
            } else {
                console.error('Failed to add item');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function handleAddLocation(e) {
        e.preventDefault();

        const form = document.getElementById('addPfLocationForm');
        const formData = new FormData(form);
        const data = {};

        formData.forEach((value, key) => {
            data[key] = value;
        });

        const locationParts = data.location.split('-');

        if (locationParts.length < 3) {
            alert('Please enter a valid location format (City - Community - Sub Community - Building/Tower)');
            return;
        }

        data.city = locationParts[0].trim();
        data.community = locationParts[1].trim();
        data.subCommunity = locationParts[2]?.trim();
        data.building = locationParts[3]?.trim();

        const fields = {
            "ufCrm26Location": data.location,
            "ufCrm26City": data.city,
            "ufCrm26Community": data.community,
            "ufCrm26SubCommunity": data.subCommunity,
            "ufCrm26Building": data.building,
        };

        addItem(1100, fields);
    }
</script>