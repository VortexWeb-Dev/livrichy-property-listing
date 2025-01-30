<div class="w-4/5 mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <form class="w-full space-y-4" id="addPropertyForm" onsubmit="handleAddProperty(event)" enctype="multipart/form-data">
            <!-- Management -->
            <?php include_once('views/components/add-property/management.php'); ?>
            <!-- Specifications -->
            <?php include_once('views/components/add-property/specifications.php'); ?>
            <!-- Property Permit -->
            <?php include_once('views/components/add-property/permit.php'); ?>
            <!-- Pricing -->
            <?php include_once('views/components/add-property/pricing.php'); ?>
            <!-- Title and Description -->
            <?php include_once('views/components/add-property/title.php'); ?>
            <!-- Amenities -->
            <?php include_once('views/components/add-property/amenities.php'); ?>
            <!-- Location -->
            <?php include_once('views/components/add-property/location.php'); ?>
            <!-- Photos and Videos -->
            <?php include_once('views/components/add-property/media.php'); ?>
            <!-- Floor Plan -->
            <?php include_once('views/components/add-property/floorplan.php'); ?>
            <!-- Documents -->
            <?php // include_once('views/components/add-property/documents.php'); 
            ?>
            <!-- Notes -->
            <?php include_once('views/components/add-property/notes.php'); ?>
            <!-- Portals -->
            <?php include_once('views/components/add-property/portals.php'); ?>
            <!-- Status -->
            <?php include_once('views/components/add-property/status.php'); ?>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.location.href = 'index.php?page=properties'" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Back
                </button>
                <button type="submit" id="submitButton" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("offering_type").addEventListener("change", function() {
        const offeringType = this.value;
        console.log(offeringType);

        if (offeringType == 'RR' || offeringType == 'CR') {
            document.getElementById("rental_period").setAttribute("required", true);
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental) <span class="text-danger">*</span>';
        } else {
            document.getElementById("rental_period").removeAttribute("required");
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental)';
        }
    })

    async function addItem(entityTypeId, fields) {
        try {
            const response = await fetch(`https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.add?entityTypeId=${entityTypeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fields
                }),
            });

            if (response.ok) {
                const data = await response.json();
                return data;
            } else {
                const errorDetails = await response.text();
                console.error(`Failed to add item. Status: ${response.status}, Details: ${errorDetails}`);
                return null;
            }
        } catch (error) {
            console.error('Error while adding item:', error);
            return null;
        }
    }

    async function handleAddProperty(e) {
        e.preventDefault();

        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').innerHTML = 'Submitting...';

        const form = document.getElementById('addPropertyForm');
        const formData = new FormData(form);
        const data = {};

        formData.forEach((value, key) => {
            data[key] = typeof value === 'string' ? value.trim() : value;
        });

        const agent = await getAgent(data.listing_agent);

        const fields = {
            "title": data.title_deed,
            "ufCrm13ReferenceNumber": data.reference,
            "ufCrm13OfferingType": data.offering_type,
            "ufCrm13PropertyType": data.property_type,
            "ufCrm13Price": data.price,
            "ufCrm13TitleEn": data.title_en,
            "ufCrm13DescriptionEn": data.description_en,
            "ufCrm13TitleAr": data.title_ar,
            "ufCrm13DescriptionAr": data.description_ar,
            "ufCrm13Size": data.size,
            "ufCrm13Bedroom": data.bedrooms,
            "ufCrm13Bathroom": data.bathrooms,
            "ufCrm13Parking": data.parkings,
            "ufCrm13Geopoints": `${data.latitude}, ${data.longitude}`,
            "ufCrm13PermitNumber": data.dtcm_permit_number,
            "ufCrm13RentalPeriod": data.rental_period,
            "ufCrm13Furnished": data.furnished,
            "ufCrm13TotalPlotSize": data.total_plot_size,
            "ufCrm13LotSize": data.lot_size,
            "ufCrm13BuildupArea": data.buildup_area,
            "ufCrm13LayoutType": data.layout_type,
            "ufCrm13ProjectName": data.project_name,
            "ufCrm13ProjectStatus": data.project_status,
            "ufCrm13Ownership": data.ownership,
            "ufCrm13Developers": data.developer,
            "ufCrm13BuildYear": data.build_year,
            "ufCrm13Availability": data.availability,
            "ufCrm13AvailableFrom": data.available_from,
            "ufCrm13PaymentMethod": data.payment_method,
            "ufCrm13DownPaymentPrice": data.downpayment_price,
            "ufCrm13NoOfCheques": data.cheques,
            "ufCrm13ServiceCharge": data.service_charge,
            "ufCrm13FinancialStatus": data.financial_status,
            "ufCrm13VideoTourUrl": data.video_tour_url,
            "ufCrm_13_360_VIEW_URL": data["360_view_url"],
            "ufCrm13QrCodePropertyBooster": data.qr_code_url,
            "ufCrm13Location": data.pf_location,
            "ufCrm13City": data.pf_city,
            "ufCrm13Community": data.pf_community,
            "ufCrm13SubCommunity": data.pf_subcommunity,
            "ufCrm13Tower": data.pf_building,
            "ufCrm13BayutLocation": data.bayut_location,
            "ufCrm13BayutCity": data.bayut_city,
            "ufCrm13BayutCommunity": data.bayut_community,
            "ufCrm13BayutSubCommunity": data.bayut_subcommunity,
            "ufCrm13BayutTower": data.bayut_building,
            "ufCrm13Latitude": data.latitude,
            "ufCrm13Longitude": data.longitude,
            "ufCrm13Status": data.status,
            "ufCrm13ReraPermitNumber": data.rera_permit_number,
            "ufCrm13ReraPermitIssueDate": data.rera_issue_date,
            "ufCrm13ReraPermitExpirationDate": data.rera_expiration_date,
            "ufCrm13DtcmPermitNumber": data.dtcm_permit_number,
            "ufCrm13ListingOwner": data.listing_owner,
            "ufCrm13LandlordName": data.landlord_name,
            "ufCrm13LandlordEmail": data.landlord_email,
            "ufCrm13LandlordContact": data.landlord_phone,
            "ufCrm13ContractExpiryDate": data.contract_expiry,
            "ufCrm13UnitNo": data.unit_no,
            "ufCrm13SaleType": data.sale_type,
            "ufCrm13BrochureDescription": data.brochure_description_1,
            "ufCrm_13_BROCHUREDESCRIPTION2": data.brochure_description_2,
            "ufCrm13HidePrice": data.hide_price == "on" ? "Y" : "N",
            "ufCrm13PfEnable": data.pf_enable == "on" ? "Y" : "N",
            "ufCrm13BayutEnable": data.bayut_enable == "on" ? "Y" : "N",
            "ufCrm13DubizzleEnable": data.dubizzle_enable == "on" ? "Y" : "N",
            "ufCrm13WebsiteEnable": data.website_enable == "on" ? "Y" : "N",
        };

        if (agent) {
            fields["ufCrm13AgentId"] = agent.ufCrm14AgentId;
            fields["ufCrm13AgentName"] = agent.ufCrm14AgentName;
            fields["ufCrm13AgentEmail"] = agent.ufCrm14AgentEmail;
            fields["ufCrm13AgentPhone"] = agent.ufCrm14AgentMobile;
            fields["ufCrm13AgentPhoto"] = agent.ufCrm14AgentPhoto;
            fields["ufCrm13AgentLicense"] = agent.ufCrm14AgentLicense;
        }

        // Notes
        const notesString = data.notes;
        if (notesString) {
            const notesArray = JSON.parse(notesString);
            if (notesArray) {
                fields["ufCrm13Notes"] = notesArray;
            }
        }

        // Amenities
        const amenitiesString = data.amenities;
        if (amenitiesString) {
            const amenitiesArray = JSON.parse(amenitiesString);
            if (amenitiesArray) {
                fields["ufCrm13Amenities"] = amenitiesArray;
            }
        }

        // Property Photos
        const photos = document.getElementById('selectedImages').value;
        if (photos) {
            const fixedPhotos = photos.replace(/\\'/g, '"');
            const photoArray = JSON.parse(fixedPhotos);
            const watermarkPath = 'assets/images/watermark.png';
            const uploadedImages = await processBase64Images(photoArray, watermarkPath);

            if (uploadedImages.length > 0) {
                fields["ufCrm13PhotoLinks"] = uploadedImages;
            }
        }

        // Floorplan
        const floorplan = document.getElementById('selectedFloorplan').value;
        if (floorplan) {
            const fixedFloorplan = floorplan.replace(/\\'/g, '"');
            const floorplanArray = JSON.parse(fixedFloorplan);
            const watermarkPath = 'assets/images/watermark.png';
            const uploadedFloorplan = await processBase64Images(floorplanArray, watermarkPath);

            if (uploadedFloorplan.length > 0) {
                fields["ufCrm13FloorPlan"] = uploadedFloorplan[0];
            }
        }

        // Documents
        // const documents = document.getElementById('documents')?.files;
        // if (documents) {
        //     if (documents.length > 0) {
        //         let documentUrls = [];

        //         for (const document of documents) {
        //             if (document.size > 10485760) {
        //                 alert('File size must be less than 10MB');
        //                 return;
        //             }
        //             const uploadedDocument = await uploadFile(document);
        //             documentUrls.push(uploadedDocument);
        //         }

        //         fields["ufCrm13Documents"] = documentUrls;
        //     }

        // }

        // Add to CRM
        const result = await addItem(1046, fields, '?page=properties');

        // Add to history
        if (result?.result?.item) {
            const newItem = result.result.item;

            const changedById = <?php echo json_encode((int)$currentUser['ID'] ?? ''); ?>;
            const changedByName = <?php echo json_encode(trim(($currentUser['NAME'] ?? '') . ' ' . ($currentUser['LAST_NAME'] ?? ''))); ?>;

            addHistory(845, 1046, newItem.id, "Property", changedById, changedByName);

            window.location.href = 'index.php?page=properties';
        } else {
            console.error("Failed to retrieve item. Invalid response structure:", result);
        }
    }
</script>