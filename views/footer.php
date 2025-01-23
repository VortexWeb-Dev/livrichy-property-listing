<!-- Copyright -->
<div class="text-center text-gray-500 p-2 bg-white/70">
    ©<?php echo date('Y'); ?> by
    <a class="text-reset fw-bold" href="https://vortexweb.cloud/" target="_blank">Vortex</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./node_modules/lodash/lodash.min.js"></script>
<script src="./node_modules/dropzone/dist/dropzone-min.js"></script>
<script src="./node_modules/preline/dist/preline.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="./node_modules/lodash/lodash.min.js"></script>
<script src="./node_modules/apexcharts/dist/apexcharts.min.js"></script>
<script src="./node_modules/preline/dist/helper-apexcharts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fabric@latest/dist/index.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
    const changedById = <?php echo json_encode((int)$currentUser['ID'] ?? ''); ?>;
    const changedByName = <?php echo json_encode(trim(($currentUser['NAME'] ?? '') . ' ' . ($currentUser['LAST_NAME'] ?? ''))); ?>;

    let historyActionMapping = {
        845: 'Created',
        846: 'Updated',
        847: 'Deleted',
        877: 'Published',
        878: 'Unpublished',
        879: 'Archived',
        880: 'Transferred Agent',
        881: 'Transferred Owner',
    }

    // Toggle Bayut and Dubizzle
    document.getElementById('toggle_bayut_dubizzle') && document.getElementById('toggle_bayut_dubizzle').addEventListener('change', function() {
        const isChecked = this.checked;
        document.getElementById('bayut_enable').checked = isChecked;
        document.getElementById('dubizzle_enable').checked = isChecked;
    });

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Format date and time
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        const dateOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        };
        const timeOptions = {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: true,
        };

        const formattedDate = date.toLocaleDateString('en-US', dateOptions);
        const formattedTime = date.toLocaleTimeString('en-US', timeOptions);

        return `${formattedDate}, ${formattedTime}`;
    }

    // Update character count
    function updateCharCount(countElement, length, maxLength) {
        titleCount = document.getElementById(countElement);
        titleCount.textContent = length;

        if (length >= maxLength) {
            titleCount.parentElement.classList.add('text-danger');
        } else {
            titleCount.parentElement.classList.remove('text-danger');
        }
    }

    // Parse and update location fields
    function updateLocationFields(location, type) {
        const locationParts = location.split('-');

        const city = locationParts[0].trim();
        const community = locationParts[1].trim();
        const subcommunity = locationParts[2].trim() || null;
        const building = locationParts[3].trim() || null;

        document.getElementById(`${type}_city`).value = city;
        document.getElementById(`${type}_community`).value = community;
        document.getElementById(`${type}_subcommunity`).value = subcommunity;
        document.getElementById(`${type}_building`).value = building;
    }

    // Update reference
    async function handleUpdateReference(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const propertyId = formData.get('propertyId');
        const newReference = formData.get('newReference');

        try {
            const response = await fetch('https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.update?entityTypeId=1046&id=' + propertyId + '&fields[ufCrm13ReferenceNumber]=' + newReference);
            const data = await response.json();
            location.reload();
        } catch (error) {
            console.error('Error updating reference:', error);
        }
    }

    // Format input date
    function formatInputDate(dateInput) {
        if (!dateInput) return null;

        const date = new Date(dateInput);

        if (isNaN(date.getTime())) return null;

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Get agent
    async function getAgent(agentId) {
        const response = await fetch(`https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.list?entityTypeId=1050&filter[ufCrm14AgentId]=${agentId}`);
        const data = await response.json();
        return data.result.items[0] || null;
    }

    // Handle action
    async function handleAction(action, propertyId, platform = null) {
        const baseUrl = 'https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4';
        let apiUrl = '';
        let reloadRequired = true;

        switch (action) {
            case 'copyLink':
                const link = `https://lightgray-kudu-834713.hostingersite.com/property-listing-livrichy/index.php?page=view-property&id=${propertyId}`;
                navigator.clipboard.writeText(link);
                alert('Link copied to clipboard.');
                reloadRequired = false;
                break;

            case 'downloadPDF':
                window.location.href = `download-pdf.php?id=${propertyId}`;
                reloadRequired = false;
                break;
            case 'downloadPDFAgent':
                window.location.href = `download-pdf-agent.php?id=${propertyId}`;
                reloadRequired = false;
                break;

            case 'duplicate':
                try {
                    const getUrl = `${baseUrl}/crm.item.get?entityTypeId=1046&id=${propertyId}&select[0]=id&select[1]=uf_*`;
                    const response = await fetch(getUrl, {
                        method: 'GET'
                    });
                    const data = await response.json();
                    const property = data.result.item;

                    let addUrl = `${baseUrl}/crm.item.add?entityTypeId=1046`;
                    for (const field in property) {
                        if (
                            field.startsWith('ufCrm13') &&
                            !['ufCrm13ReferenceNumber', 'ufCrm13TitleEn', 'ufCrm13Status', 'ufCrm13PhotoLinks', 'ufCrm13Documents', 'ufCrm13Notes'].includes(field)
                        ) {
                            addUrl += `&fields[${field}]=${encodeURIComponent(property[field])}`;
                        }
                    }

                    if (property['ufCrm13PhotoLinks']) {
                        property['ufCrm13PhotoLinks'].forEach((photoLink, index) => {
                            addUrl += `&fields[ufCrm13PhotoLinks][${index}]=${encodeURIComponent(photoLink)}`;
                        });
                    }

                    if (property['ufCrm13Documents']) {
                        property['ufCrm13Documents'].forEach((document, index) => {
                            addUrl += `&fields[ufCrm13Documents][${index}]=${encodeURIComponent(document)}`;
                        });
                    }

                    if (property['ufCrm13Notes']) {
                        property['ufCrm13Notes'].forEach((note, index) => {
                            addUrl += `&fields[ufCrm13Notes][${index}]=${encodeURIComponent(note)}`;
                        });
                    }

                    addUrl += `&fields[ufCrm13TitleEn]=${encodeURIComponent(property.ufCrm13TitleEn + ' (Duplicate)')}`;
                    addUrl += `&fields[ufCrm13ReferenceNumber]=${encodeURIComponent(property.ufCrm13ReferenceNumber) + '-duplicate'}`;
                    addUrl += `&fields[ufCrm13Status]=DRAFT`;

                    const result = await fetch(addUrl, {
                        method: 'GET'
                    });

                    if (result.ok) {
                        const response = await result.json();
                        const newPropertyId = response.result.item.id;

                        // Add to history
                        const changedById = <?php echo json_encode((int)$currentUser['ID'] ?? ''); ?>;
                        const changedByName = <?php echo json_encode(trim(($currentUser['NAME'] ?? '') . ' ' . ($currentUser['LAST_NAME'] ?? ''))); ?>;

                        addHistory(845, 1046, newPropertyId, "Property", changedById, changedByName, 'Duplicated from ' + propertyId);
                    }
                } catch (error) {
                    console.error('Error duplicating property:', error);
                }
                break;

            case 'publish':
                apiUrl = `${baseUrl}/crm.item.update?entityTypeId=1046&id=${propertyId}&fields[ufCrm13Status]=PUBLISHED`;
                let publishNote = ''
                if (platform) {
                    apiUrl += `&fields[ufCrm13${platform.charAt(0).toUpperCase() + platform.slice(1)}Enable]=Y`;
                    publishNote = `Published on ${platform}`
                } else {
                    apiUrl += `&fields[ufCrm13PfEnable]=Y&fields[ufCrm13BayutEnable]=Y&fields[ufCrm13DubizzleEnable]=Y&fields[ufCrm13WebsiteEnable]=Y&fields[ufCrm13Status]=PUBLISHED`;
                    publishNote = 'Published on all platforms'
                }
                addHistory(877, 1046, propertyId, "Property", changedById, changedByName, publishNote);
                break;

            case 'unpublish':
                apiUrl = `${baseUrl}/crm.item.update?entityTypeId=1046&id=${propertyId}`;
                let unpublishNote = ''
                if (platform) {
                    apiUrl += `&fields[ufCrm13${platform.charAt(0).toUpperCase() + platform.slice(1)}Enable]=N`;
                    unpublishNote = `Unpublished from ${platform}`
                } else {
                    apiUrl += `&fields[ufCrm13PfEnable]=N&fields[ufCrm13BayutEnable]=N&fields[ufCrm13DubizzleEnable]=N&fields[ufCrm13WebsiteEnable]=N&fields[ufCrm13Status]=UNPUBLISHED`;
                    unpublishNote = 'Unpublished from all platforms'
                }
                addHistory(878, 1046, propertyId, "Property", changedById, changedByName, unpublishNote);
                break;

            case 'archive':
                if (confirm('Are you sure you want to archive this property?')) {
                    apiUrl = `${baseUrl}/crm.item.update?entityTypeId=1046&id=${propertyId}&fields[ufCrm13Status]=ARCHIVED`;
                    addHistory(879, 1046, propertyId, "Property", changedById, changedByName);
                } else {
                    reloadRequired = false;
                }
                break;

            case 'delete':
                if (confirm('Are you sure you want to delete this property?')) {
                    try {
                        // First get property details to find image URLs
                        const getPropertyUrl = `${baseUrl}/crm.item.get?entityTypeId=1046&id=${propertyId}`;
                        const propertyResponse = await fetch(getPropertyUrl);
                        const propertyData = await propertyResponse.json();

                        if (propertyData.result && propertyData.result.item) {
                            const property = propertyData.result.item;
                            console.log('Property data for deletion:', property);

                            // Delete images from S3
                            if (property.ufCrm13PhotoLinks && Array.isArray(property.ufCrm13PhotoLinks)) {
                                console.log('Found photo links:', property.ufCrm13PhotoLinks);
                                for (const imageUrl of property.ufCrm13PhotoLinks) {
                                    try {
                                        console.log('Attempting to delete image:', imageUrl);
                                        const response = await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: imageUrl
                                            })
                                        });
                                        const result = await response.json();
                                        console.log('Delete response:', result);
                                        if (!result.success) {
                                            console.error(`Failed to delete image: ${result.error}`);
                                        }
                                    } catch (error) {
                                        console.error(`Error deleting S3 object: ${imageUrl}`, error);
                                    }
                                }
                            }

                            // Delete floorplan from S3 if exists
                            if (property.ufCrm13FloorPlan) {
                                try {
                                    console.log('Attempting to delete floorplan:', property.ufCrm13FloorPlan);
                                    const response = await fetch('./delete-s3-object.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            fileUrl: property.ufCrm13FloorPlan
                                        })
                                    });
                                    const result = await response.json();
                                    console.log('Floorplan delete response:', result);
                                    if (!result.success) {
                                        console.error(`Failed to delete floorplan: ${result.error}`);
                                    }
                                } catch (error) {
                                    console.error(`Error deleting S3 floorplan: ${property.ufCrm13FloorPlan}`, error);
                                }
                            }

                            // Delete documents from S3
                            if (property.ufCrm13Documents && Array.isArray(property.ufCrm13Documents)) {
                                console.log('Found documents:', property.ufCrm13Documents);
                                for (const docUrl of property.ufCrm13Documents) {
                                    try {
                                        console.log('Attempting to delete document:', docUrl);
                                        const response = await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: docUrl
                                            })
                                        });
                                        const result = await response.json();
                                        console.log('Delete response:', result);
                                        if (!result.success) {
                                            console.error(`Failed to delete document: ${result.error}`);
                                        }
                                    } catch (error) {
                                        console.error(`Error deleting S3 document: ${docUrl}`, error);
                                    }
                                }
                            }

                            // Add to history
                            const changedById = <?php echo json_encode((int)$currentUser['ID'] ?? ''); ?>;
                            const changedByName = <?php echo json_encode(trim(($currentUser['NAME'] ?? '') . ' ' . ($currentUser['LAST_NAME'] ?? ''))); ?>;

                            addHistory(847, 1046, property.id, 'Property', changedById, changedByName);
                        }

                        // Now delete the property from CRM
                        apiUrl = `${baseUrl}/crm.item.delete?entityTypeId=1046&id=${propertyId}`;
                    } catch (error) {
                        console.error('Error in delete process:', error);
                        reloadRequired = false;
                    }
                } else {
                    reloadRequired = false;
                }
                break;

            default:
                console.error('Invalid action:', action);
                reloadRequired = false;
        }

        if (apiUrl) {
            try {
                await fetch(apiUrl, {
                    method: 'GET'
                });
            } catch (error) {
                console.error(`Error executing ${action}:`, error);
            }
        }

        if (reloadRequired) {
            location.reload();
        }
    }

    // Bulk action
    async function handleBulkAction(action, platform) {
        const checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        const propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (propertyIds.length === 0) {
            alert('Please select at least one property.');
            return;
        }

        if (confirm(`Are you sure you want to ${action} the selected properties?`)) {
            try {
                const baseUrl = 'https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4';
                const apiUrl = `${baseUrl}/crm.item.${action === 'delete' ? 'delete' : 'update'}?entityTypeId=1046`;

                const platformFieldMapping = {
                    pf: 'ufCrm13PfEnable',
                    bayut: 'ufCrm13BayutEnable',
                    dubizzle: 'ufCrm13DubizzleEnable',
                    website: 'ufCrm13WebsiteEnable'
                };

                // If action is delete, first get all property details to find image URLs
                if (action === 'delete') {
                    for (const propertyId of propertyIds) {
                        try {
                            // Get property details to find image URLs
                            const getPropertyUrl = `${baseUrl}/crm.item.get?entityTypeId=1046&id=${propertyId}`;
                            const propertyResponse = await fetch(getPropertyUrl);
                            const propertyData = await propertyResponse.json();
                            console.log('Property data:', propertyData);
                            if (propertyData.result && propertyData.result.item) {
                                const property = propertyData.result.item;

                                // Delete images from S3
                                if (property.ufCrm13PhotoLinks && Array.isArray(property.ufCrm13PhotoLinks)) {
                                    for (const imageUrl of property.ufCrm13PhotoLinks) {
                                        try {
                                            await fetch('./delete-s3-object.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    fileUrl: imageUrl
                                                })
                                            });
                                        } catch (error) {
                                            console.error(`Error deleting S3 object: ${imageUrl}`, error);
                                        }
                                    }
                                }

                                // Delete floorplan from S3 if exists
                                if (property.ufCrm13FloorPlan) {
                                    try {
                                        await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: property.ufCrm13FloorPlan
                                            })
                                        });
                                    } catch (error) {
                                        console.error(`Error deleting S3 floorplan: ${property.ufCrm13FloorPlan}`, error);
                                    }
                                }

                                // Delete documents from S3
                                if (property.ufCrm13Documents && Array.isArray(property.ufCrm13Documents)) {
                                    for (const docUrl of property.ufCrm13Documents) {
                                        try {
                                            await fetch('./delete-s3-object.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    fileUrl: docUrl
                                                })
                                            });
                                        } catch (error) {
                                            console.error(`Error deleting S3 document: ${docUrl}`, error);
                                        }
                                    }
                                }

                                // Add to history
                                addHistory(847, 1046, property.id, 'Property', changedById, changedByName, 'Deleted using bulk action');
                            }
                        } catch (error) {
                            console.error(`Error getting property details for deletion: ${propertyId}`, error);
                        }
                    }
                }

                const requests = propertyIds.map(propertyId => {
                    let url = `${apiUrl}&id=${propertyId}`;

                    if (action === 'publish') {
                        url += '&fields[ufCrm13Status]=PUBLISHED';
                        let publishNote = ''

                        if (platformFieldMapping[platform]) {
                            url += `&fields[${platformFieldMapping[platform]}]=Y`;
                            publishNote = `Published on ${platform}`

                        } else {
                            url += `&fields[ufCrm13PfEnable]=Y&fields[ufCrm13BayutEnable]=Y&fields[ufCrm13DubizzleEnable]=Y&fields[ufCrm13WebsiteEnable]=Y`;
                            publishNote = 'Published on all platforms'
                        }

                        addHistory(877, 1046, propertyId, "Property", changedById, changedByName, publishNote);
                    } else if (action === 'unpublish') {
                        let unpublishNote = ''
                        if (platformFieldMapping[platform]) {
                            url += `&fields[${platformFieldMapping[platform]}]=N`;
                            unpublishNote = `Unpublished from ${platform}`
                        } else {
                            url += `&fields[ufCrm13PfEnable]=N&fields[ufCrm13BayutEnable]=N&fields[ufCrm13DubizzleEnable]=N&fields[ufCrm13WebsiteEnable]=N&fields[ufCrm13Status]=UNPUBLISHED`;
                            unpublishNote = 'Unpublished from all platforms'
                        }

                        addHistory(878, 1046, propertyId, "Property", changedById, changedByName, unpublishNote);
                    } else if (action === 'archive') {
                        url += '&fields[ufCrm13Status]=ARCHIVED';
                        addHistory(879, 1046, propertyId, "Property", changedById, changedByName, 'Archived using bulk action');
                    }

                    return fetch(url, {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(data => {})
                        .catch(error => {
                            console.error(`Error updating property ${propertyId}:`, error);
                        });
                });

                // Wait for all requests to finish
                await Promise.all(requests);

                location.reload();
            } catch (error) {
                console.error('Error handling bulk action:', error);
            }
        }
    }

    // Function to add watermark to the image
    function addWatermark(imageElement, watermarkImagePath) {
        return new Promise((resolve, reject) => {
            const watermarkImage = new Image();
            watermarkImage.src = watermarkImagePath;

            watermarkImage.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const width = imageElement.width;
                const height = imageElement.height;

                canvas.width = width;
                canvas.height = height;

                ctx.drawImage(imageElement, 0, 0, width, height);

                const watermarkAspect = watermarkImage.width / watermarkImage.height;
                const imageAspect = width / height;

                let watermarkWidth, watermarkHeight;

                if (watermarkAspect > imageAspect) {
                    watermarkWidth = width * 0.6;
                    watermarkHeight = watermarkWidth / watermarkAspect;
                } else {
                    watermarkHeight = height * 0.6;
                    watermarkWidth = watermarkHeight * watermarkAspect;
                }

                const xPosition = (width - watermarkWidth) / 2;
                const yPosition = (height - watermarkHeight) / 2;

                ctx.drawImage(watermarkImage, xPosition, yPosition, watermarkWidth, watermarkHeight);
                const watermarkedImage = canvas.toDataURL('image/jpeg', 0.8);
                resolve(watermarkedImage);
            };

            watermarkImage.onerror = function() {
                reject('Failed to load watermark image.');
            };
        });
    }

    // Function to add watermark text to the image
    function addWatermarkText(imageElement, watermarkText) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const width = imageElement.width;
            const height = imageElement.height;

            canvas.width = width;
            canvas.height = height;

            ctx.drawImage(imageElement, 0, 0, width, height);

            // Set the watermark text properties
            ctx.font = '360px Arial'; // You can adjust the font size here
            ctx.fillStyle = 'rgba(255, 255, 255, 0.6)'; // White color with 50% transparency
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Add the watermark text to the image (centered)
            ctx.fillText(watermarkText, width / 2, height / 2);

            // Convert the image to JPEG with reduced quality (optional)
            const watermarkedImage = canvas.toDataURL('image/jpeg', 0.7); // Adjust quality as needed
            resolve(watermarkedImage);
        });
    }

    // Function to upload a file
    function uploadFile(file, isDocument = false) {
        const formData = new FormData();
        formData.append('file', file);

        if (isDocument) {
            formData.append('isDocument', 'true');
        }

        return fetch('upload-file.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    return data.url;
                } else {
                    console.error('Error uploading file (PHP backend):', data.error);
                    return null;
                }
            })
            .catch((error) => {
                console.error("Error uploading file:", error);
                return null;
            });
    }

    // Process base64 images
    async function processBase64Images(base64Images, watermarkPath) {
        const photoPaths = [];

        for (const base64Image of base64Images) {
            const regex = /^data:image\/(\w+);base64,/;
            const matches = base64Image.match(regex);

            if (matches) {
                const base64Data = base64Image.replace(regex, '');
                const imageData = atob(base64Data);

                const blob = new Blob([new Uint8Array(imageData.split('').map(c => c.charCodeAt(0)))], {
                    type: `image/${matches[1]}`,
                });
                const imageUrl = URL.createObjectURL(blob);

                const imageElement = new Image();
                imageElement.src = imageUrl;

                await new Promise((resolve, reject) => {
                    imageElement.onload = async () => {
                        try {
                            // Add watermark to the image
                            const watermarkedDataUrl = await addWatermark(imageElement, watermarkPath);
                            // const watermarkedDataUrl = await addWatermarkText(imageElement, 'LIVRICHY');
                            // const watermarkedDataUrl = await addWatermarkWithFabric(imageElement, watermarkPath);

                            // Convert the data URL to a Blob
                            const watermarkedBlob = dataURLToBlob(watermarkedDataUrl);

                            // Upload the watermarked Blob
                            const uploadedUrl = await uploadFile(watermarkedBlob);

                            if (uploadedUrl) {
                                photoPaths.push(uploadedUrl); // Add the uploaded URL to the photoPaths array
                            } else {
                                console.error('Error uploading photo from base64 data');
                            }

                            resolve();
                        } catch (error) {
                            console.error('Error processing watermarking or uploading:', error);
                            reject(error);
                        } finally {
                            URL.revokeObjectURL(imageUrl); // Clean up the object URL
                        }
                    };

                    imageElement.onerror = (error) => {
                        console.error('Failed to load image from URL:', error);
                        reject(error);
                    };
                });
            } else {
                console.error('Invalid base64 image data');
            }
        }

        return photoPaths;
    }

    // Function to get the name of an amenity
    function getAmenityName(amenityId) {
        const amenities = [{
                id: 'GV',
                label: 'Golf view'
            },
            {
                id: 'CW',
                label: 'City view'
            },
            {
                id: 'NO',
                label: 'North orientation'
            },
            {
                id: 'SO',
                label: 'South orientation'
            },
            {
                id: 'EO',
                label: 'East orientation'
            },
            {
                id: 'WO',
                label: 'West orientation'
            },
            {
                id: 'NS',
                label: 'Near school'
            },
            {
                id: 'HO',
                label: 'Near hospital'
            },
            {
                id: 'TR',
                label: 'Terrace'
            },
            {
                id: 'NM',
                label: 'Near mosque'
            },
            {
                id: 'SM',
                label: 'Near supermarket'
            },
            {
                id: 'ML',
                label: 'Near mall'
            },
            {
                id: 'PT',
                label: 'Near public transportation'
            },
            {
                id: 'MO',
                label: 'Near metro'
            },
            {
                id: 'VT',
                label: 'Near veterinary'
            },
            {
                id: 'BC',
                label: 'Beach access'
            },
            {
                id: 'PK',
                label: 'Public parks'
            },
            {
                id: 'RT',
                label: 'Near restaurants'
            },
            {
                id: 'NG',
                label: 'Near Golf'
            },
            {
                id: 'AP',
                label: 'Near airport'
            },
            {
                id: 'CS',
                label: 'Concierge Service'
            },
            {
                id: 'SS',
                label: 'Spa'
            },
            {
                id: 'SY',
                label: 'Shared Gym'
            },
            {
                id: 'MS',
                label: 'Maid Service'
            },
            {
                id: 'WC',
                label: 'Walk-in Closet'
            },
            {
                id: 'HT',
                label: 'Heating'
            },
            {
                id: 'GF',
                label: 'Ground floor'
            },
            {
                id: 'SV',
                label: 'Server room'
            },
            {
                id: 'DN',
                label: 'Pantry'
            },
            {
                id: 'RA',
                label: 'Reception area'
            },
            {
                id: 'VP',
                label: 'Visitors parking'
            },
            {
                id: 'OP',
                label: 'Office partitions'
            },
            {
                id: 'SH',
                label: 'Core and Shell'
            },
            {
                id: 'CD',
                label: 'Children daycare'
            },
            {
                id: 'CL',
                label: 'Cleaning services'
            },
            {
                id: 'NH',
                label: 'Near Hotel'
            },
            {
                id: 'CR',
                label: 'Conference room'
            },
            {
                id: 'BL',
                label: 'View of Landmark'
            },
            {
                id: 'PR',
                label: 'Children Play Area'
            },
            {
                id: 'BH',
                label: 'Beach Access'
            }
        ];

        return amenities.find(amenity => amenity.id === amenityId)?.label || amenityId;
    }

    // Function to get the ID of an amenity
    function getAmenityId(amenityName) {
        console.log(amenityName);

        const amenities = [{
                id: 'GV',
                label: 'Golf view'
            },
            {
                id: 'CW',
                label: 'City view'
            },
            {
                id: 'NO',
                label: 'North orientation'
            },
            {
                id: 'SO',
                label: 'South orientation'
            },
            {
                id: 'EO',
                label: 'East orientation'
            },
            {
                id: 'WO',
                label: 'West orientation'
            },
            {
                id: 'NS',
                label: 'Near school'
            },
            {
                id: 'HO',
                label: 'Near hospital'
            },
            {
                id: 'TR',
                label: 'Terrace'
            },
            {
                id: 'NM',
                label: 'Near mosque'
            },
            {
                id: 'SM',
                label: 'Near supermarket'
            },
            {
                id: 'ML',
                label: 'Near mall'
            },
            {
                id: 'PT',
                label: 'Near public transportation'
            },
            {
                id: 'MO',
                label: 'Near metro'
            },
            {
                id: 'VT',
                label: 'Near veterinary'
            },
            {
                id: 'BC',
                label: 'Beach access'
            },
            {
                id: 'PK',
                label: 'Public parks'
            },
            {
                id: 'RT',
                label: 'Near restaurants'
            },
            {
                id: 'NG',
                label: 'Near Golf'
            },
            {
                id: 'AP',
                label: 'Near airport'
            },
            {
                id: 'CS',
                label: 'Concierge Service'
            },
            {
                id: 'SS',
                label: 'Spa'
            },
            {
                id: 'SY',
                label: 'Shared Gym'
            },
            {
                id: 'MS',
                label: 'Maid Service'
            },
            {
                id: 'WC',
                label: 'Walk-in Closet'
            },
            {
                id: 'HT',
                label: 'Heating'
            },
            {
                id: 'GF',
                label: 'Ground floor'
            },
            {
                id: 'SV',
                label: 'Server room'
            },
            {
                id: 'DN',
                label: 'Pantry'
            },
            {
                id: 'RA',
                label: 'Reception area'
            },
            {
                id: 'VP',
                label: 'Visitors parking'
            },
            {
                id: 'OP',
                label: 'Office partitions'
            },
            {
                id: 'SH',
                label: 'Core and Shell'
            },
            {
                id: 'CD',
                label: 'Children daycare'
            },
            {
                id: 'CL',
                label: 'Cleaning services'
            },
            {
                id: 'NH',
                label: 'Near Hotel'
            },
            {
                id: 'CR',
                label: 'Conference room'
            },
            {
                id: 'BL',
                label: 'View of Landmark'
            },
            {
                id: 'PR',
                label: 'Children Play Area'
            },
            {
                id: 'BH',
                label: 'Beach Access'
            }
        ];

        console.log(amenities.find(amenity => amenity.label === amenityName)?.id || amenityName)

        return amenities.find(amenity => amenity.label === amenityName)?.id || amenityName;
    }

    // Function to convert data URL to Blob
    function dataURLToBlob(dataURL) {
        const byteString = atob(dataURL.split(',')[1]);
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const uintArray = new Uint8Array(arrayBuffer);
        for (let i = 0; i < byteString.length; i++) {
            uintArray[i] = byteString.charCodeAt(i);
        }
        return new Blob([uintArray], {
            type: 'image/png'
        });
    }

    // Function to fetch a property
    async function fetchProperty(id) {
        const url = `https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.get?entityTypeId=1046&id=${id}`;
        const response = await fetch(url);
        const data = await response.json();
        if (data.result && data.result.item) {
            const property = data.result.item;

            // Management
            document.getElementById('reference').value = property.ufCrm13ReferenceNumber;
            document.getElementById('landlord_name').value = property.ufCrm13LandlordName;
            document.getElementById('landlord_email').value = property.ufCrm13LandlordEmail;
            document.getElementById('landlord_phone').value = property.ufCrm13LandlordContact;
            Array.from(document.getElementById('availability').options).forEach(option => {
                if (option.value == property.ufCrm13Availability) option.selected = true;
            });
            document.getElementById('available_from').value = formatInputDate(property.ufCrm13AvailableFrom);
            document.getElementById('contract_expiry').value = formatInputDate(property.ufCrm13ContractExpiryDate);

            // Specifications
            document.getElementById('title_deed').value = property.title;
            document.getElementById('size').value = property.ufCrm13Size;
            document.getElementById('unit_no').value = property.ufCrm13UnitNo;
            document.getElementById('bathrooms').value = property.ufCrm13Bathroom;
            document.getElementById('parkings').value = property.ufCrm13Parking;
            document.getElementById('total_plot_size').value = property.ufCrm13TotalPlotSize;
            document.getElementById('lot_size').value = property.ufCrm13LotSize;
            document.getElementById('buildup_area').value = property.ufCrm13BuildupArea;
            document.getElementById('layout_type').value = property.ufCrm13LayoutType;
            document.getElementById('project_name').value = property.ufCrm13ProjectName;
            document.getElementById('build_year').value = property.ufCrm13BuildYear;
            Array.from(document.getElementById('property_type').options).forEach(option => {
                if (option.value === property.ufCrm13PropertyType) option.selected = true;
            });
            Array.from(document.getElementById('offering_type').options).forEach(option => {
                if (option.value === property.ufCrm13OfferingType) option.selected = true;
            });
            Array.from(document.getElementById('bedrooms').options).forEach(option => {
                if (option.value == property.ufCrm13Bedroom) option.selected = true;
            });
            Array.from(document.getElementById('furnished').options).forEach(option => {
                if (option.value == property.ufCrm13Furnished) option.selected = true;
            });
            Array.from(document.getElementById('project_status').options).forEach(option => {
                if (option.value == property.ufCrm13ProjectStatus) option.selected = true;
            });
            Array.from(document.getElementById('sale_type').options).forEach(option => {
                if (option.value == property.ufCrm13SaleType) option.selected = true;
            });
            Array.from(document.getElementById('ownership').options).forEach(option => {
                if (option.value == property.ufCrm13Ownership) option.selected = true;
            });

            // Property Permit
            document.getElementById('rera_permit_number').value = property.ufCrm13ReraPermitNumber
            document.getElementById('dtcm_permit_number').value = property.ufCrm13DtcmPermitNumber
            document.getElementById('rera_issue_date').value = formatInputDate(property.ufCrm13ReraPermitIssueDate);
            document.getElementById('rera_expiration_date').value = formatInputDate(property.ufCrm13ReraPermitExpirationDate);

            // Pricing
            document.getElementById('price').value = property.ufCrm13Price;
            document.getElementById('payment_method').value = property.ufCrm13PaymentMethod;
            document.getElementById('downpayment_price').value = property.ufCrm13DownPaymentPrice;
            document.getElementById('service_charge').value = property.ufCrm13ServiceCharge;
            property.ufCrm13HidePrice == "Y" ? document.getElementById('hide_price').checked = true : document.getElementById('hide_price').checked = false;
            Array.from(document.getElementById('rental_period').options).forEach(option => {
                if (option.value == property.ufCrm13RentalPeriod) option.selected = true;
            });
            Array.from(document.getElementById('cheques').options).forEach(option => {
                if (option.value == property.ufCrm13NoOfCheques) option.selected = true;
            });
            Array.from(document.getElementById('financial_status').options).forEach(option => {
                if (option.value == property.ufCrm13FinancialStatus) option.selected = true;
            });

            // Title and Description
            document.getElementById('title_en').value = property.ufCrm13TitleEn;
            document.getElementById('description_en').textContent = property.ufCrm13DescriptionEn;
            document.getElementById('title_ar').value = property.ufCrm13TitleAr;
            document.getElementById('description_ar').textContent = property.ufCrm13DescriptionAr;
            document.getElementById('brochure_description_1').textContent = property.ufCrm13BrochureDescription;
            document.getElementById('brochure_description_2').textContent = property.ufCrm_13_BROCHUREDESCRIPTION2;

            document.getElementById('titleEnCount').textContent = document.getElementById('title_en').value.length;
            document.getElementById('descriptionEnCount').textContent = document.getElementById('description_en').textContent.length;
            document.getElementById('titleArCount').textContent = document.getElementById('title_ar').value.length;
            document.getElementById('descriptionArCount').textContent = document.getElementById('description_ar').textContent.length;
            document.getElementById('brochureDescription1Count').textContent = document.getElementById('brochure_description_1').textContent.length;
            document.getElementById('brochureDescription2Count').textContent = document.getElementById('brochure_description_2').textContent.length;

            // Location
            document.getElementById('pf_location').value = property.ufCrm13Location;
            document.getElementById('pf_city').value = property.ufCrm13City;
            document.getElementById('pf_community').value = property.ufCrm13Community;
            document.getElementById('pf_subcommunity').value = property.ufCrm13SubCommunity;
            document.getElementById('pf_building').value = property.ufCrm13Tower;
            document.getElementById('bayut_location').value = property.ufCrm13BayutLocation;
            document.getElementById('bayut_city').value = property.ufCrm13BayutCity;
            document.getElementById('bayut_community').value = property.ufCrm13BayutCommunity;
            document.getElementById('bayut_subcommunity').value = property.ufCrm13BayutSubCommunity;
            document.getElementById('bayut_building').value = property.ufCrm13BayutTower;

            document.getElementById('latitude').value = property.ufCrm13Latitude;
            document.getElementById('longitude').value = property.ufCrm13Longitude;

            // Photos and Videos
            document.getElementById('video_tour_url').value = property.ufCrm13VideoTourUrl;
            document.getElementById('360_view_url').value = property.ufCrm_13_360_VIEW_URL;
            document.getElementById('qr_code_url').value = property.ufCrm13QrCodePropertyBooster;
            // Photos
            // Floor Plan

            // Portals
            property.ufCrm13PfEnable == "Y" ? document.getElementById('pf_enable').checked = true : document.getElementById('pf_enable').checked = false;
            property.ufCrm13BayutEnable == "Y" ? document.getElementById('bayut_enable').checked = true : document.getElementById('bayut_enable').checked = false;
            property.ufCrm13DubizzleEnable == "Y" ? document.getElementById('dubizzle_enable').checked = true : document.getElementById('dubizzle_enable').checked = false;
            property.ufCrm13WebsiteEnable == "Y" ? document.getElementById('website_enable').checked = true : document.getElementById('website_enable').checked = false;
            if (document.getElementById('dubizzle_enable').checked && document.getElementById('bayut_enable').value) {
                toggle_bayut_dubizzle.checked = true;
            }

            switch (property.ufCrm13Status) {
                case 'PUBLISHED':
                    if (document.getElementById('publish')) document.getElementById('publish').checked = true;
                    break;
                case 'UNPUBLISHED':
                    if (document.getElementById('unpublish')) document.getElementById('unpublish').checked = true;
                    break;
                case 'LIVE':
                    if (document.getElementById('live')) document.getElementById('live').checked = true;
                    break;
                case 'DRAFT':
                    if (document.getElementById('draft')) document.getElementById('draft').checked = true;
                    break;
                case 'ARCHIVED':
                    if (document.getElementById('archive')) document.getElementById('archive').checked = true;
                    break;
                case 'POCKET':
                    if (document.getElementById('pocket')) getElementById('pocket').checked = true;
                    break;
            }

            function ensureOptionExistsAndSelect(selectElementId, value, label) {
                const selectElement = document.getElementById(selectElementId);
                const existingOption = document.querySelector(`#${selectElementId} option[value="${value}"]`);

                if (!existingOption) {
                    const newOption = document.createElement('option');
                    newOption.value = value;
                    newOption.textContent = label || 'Unknown Option';
                    newOption.selected = true;
                    selectElement.appendChild(newOption);
                } else {
                    existingOption.selected = true;
                }
            }

            ensureOptionExistsAndSelect('listing_agent', property.ufCrm13AgentId, property.ufCrm13AgentName);
            ensureOptionExistsAndSelect('listing_owner', property.ufCrm13ListingOwner, property.ufCrm13ListingOwner);
            ensureOptionExistsAndSelect('developer', property.ufCrm13Developers, property.ufCrm13Developers);

            // Notes
            function addExistingNote(note) {
                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

                li.innerHTML = `
                    ${note} 
                    <button class="text-red-500 hover:text-red-700" onclick="removeNote(this)">×</button>
                `;

                document.getElementById("notesList").appendChild(li);
                updateNotesInput();
            }

            if (property.ufCrm13Notes.length > 0) {
                property.ufCrm13Notes.forEach(note => {
                    addExistingNote(note);
                });
            }

            // Amenities
            function addExistingAmenity(amenity) {
                if (!selectedAmenities.some(a => a.id === amenity)) {
                    selectedAmenities.push({
                        id: amenity,
                        label: getAmenityName(amenity)
                    });
                }

                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

                li.innerHTML = `
                    ${getAmenityName(amenity)} 
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeAmenity('${amenity}')">×</button>
                `;

                document.getElementById("selectedAmenities").appendChild(li);
                updateAmenitiesInput();
            }

            if (property.ufCrm13Amenities && property.ufCrm13Amenities.length > 0) {
                property.ufCrm13Amenities.forEach(amenity => {
                    addExistingAmenity(amenity);
                });
            }


            return property;

        } else {
            console.error('Invalid property data:', data);
            document.getElementById('property-details').textContent = 'Failed to load property details.';
        }
    }

    // Function to check if any property is selected
    function isPropertySelected() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        return propertyIds && propertyIds.length > 0;
    }

    // Function to select and add properties to agent transfer form
    function selectAndAddPropertiesToAgentTransfer() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (!isPropertySelected()) {
            return alert('Please select at least one property.');
        }

        document.getElementById('transferAgentPropertyIds').value = propertyIds.join(',');

        const agentModal = new bootstrap.Modal(document.getElementById('transferAgentModal'));
        agentModal.show();
    }

    // Function to select and add properties to owner transfer form
    function selectAndAddPropertiesToOwnerTransfer() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (!isPropertySelected()) {
            return alert('Please select at least one property.');
        }

        document.getElementById('transferOwnerPropertyIds').value = propertyIds.join(',');


        const ownerModal = new bootstrap.Modal(document.getElementById('transferOwnerModal'));
        ownerModal.show();
    }

    // Function to calculate square meters
    function sqftToSqm(sqft) {
        const sqm = sqft * 0.092903;
        return parseFloat(sqm.toFixed(2));
    }

    // Function to add history
    async function addHistory(action, entityId, itemId, entityName, changedById, changedByName, note = null) {
        const apiUrl = `https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.add`;

        const validActions = [
            845, // Create
            846, // Update
            847, // Delete
            877, // Publish
            878, // Unpublish
            879, // Archive
            880, // Transfer Agent
            881, // Transfer Owner
        ];
        if (!validActions.includes(action)) {
            console.error("Invalid action type. Must be 845, 846, 847, 877, 878, 879, 880, or 881");
            return;
        }

        const payload = {
            entityTypeId: 1108,
            fields: {
                ufCrm27Entity: entityId,
                ufCrm27Item: itemId,
                ufCrm27Action: action,
                ufCrm27EntityName: entityName,
                ufCrm27ChangedBy: changedById,
                ufCrm27ChangedByName: changedByName,
            },
        };

        if (note) {
            payload.fields.ufCrm27Note = note;
        }

        try {
            const response = await fetch(apiUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (data.result) {
                console.log("History added successfully:", data);
            } else {
                console.error("Failed to add history:", data.error);
            }
        } catch (error) {

            console.error("Error while adding history:", error);
        }
    }
</script>

</body>

</html>