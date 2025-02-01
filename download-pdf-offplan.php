<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offplan Brochure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Montserrat", serif;
        }

        body {
            background-color: #ffffff;
            padding: 20px;
        }

        img {
            object-fit: cover;
            object-position: center;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .content-section {
            padding: 20px;
        }

        .image-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 10px;
            margin-bottom: 30px;
        }

        .main-image {
            height: 500px;
            width: 100%;
            object-fit: cover;
        }

        .side-images {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 20px;
        }

        .side-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
        }

        .property-title {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .developer {
            font-size: 18px;
            color: #1E1E1E;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .location {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #333;
        }

        .location-icon {
            width: 20px;
            height: 20px;
        }

        .description {
            color: #2E2E2E;
            line-height: 30px;
            margin-bottom: 20px;
            max-width: 800px;
        }

        .apartment-type {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .apartment-icon {
            width: 20px;
            height: 20px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .prices {
            margin-bottom: 20px;
        }

        .price-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .price-aed {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .delivery-date {
            color: #666;
            font-size: 14px;
        }

        .info-box {
            border: 1px solid #C5AC62;
            padding: 25px;
            border-radius: 2px;
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .amenities-list,
        .location-list {
            list-style: none;
            font-size: 14px;
        }

        .amenity-item,
        .location-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: #1E1E1E;
            font-weight: 400;
        }

        .location-item span {
            color: #666;
        }

        .location-time {
            font-weight: 500;
        }

        .payment-plan {
            margin-top: 30px;
        }

        .payment-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-list {
            list-style: none;
        }

        .payment-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: #333;
        }

        .logo {
            margin-top: 50px;
            width: 150px;
        }

        .apartment-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-item img {
            width: 20px;
            height: 20px;
        }

        .agent-card {
            width: 400px;
            height: 200px;
            background-color: #C5AC6226;
            border-radius: 2px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 20px;
            font-family: Arial, sans-serif;
        }

        .agent-info {
            flex: 1;
        }

        .agent-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #18181A;
        }

        .agent-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #18181A;
        }

        .agent-contact {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .agent-phone {
            color: #18181A;
            font-weight: 600;
        }

        .agent-photo {
            width: 100px;
            height: 100px;
            background-color: #eee;
            border-radius: 4px;
            align-self: center;
        }

        .agent-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
    <div class="container wrapper">
        <div class="image-grid">
            <img src="" alt="Trinity Exterior" class="main-image" crossorigin="anonymous">
            <div class="side-images">
                <img src="" alt="Interior View 1" class="side-image side-image-1" crossorigin="anonymous">
                <img src="" alt="Interior View 2" class="side-image side-image-2" crossorigin="anonymous">
            </div>
        </div>

        <div class="content-section">
            <div class="content-grid">
                <div class="left-content">
                    <h1 class="property-title">TRINITY</h1>
                    <div class="developer"></div>

                    <div class="location">
                        <img src="./assets/images/pin.svg" alt="">
                        <span class="location-text">DUBAI, ARJAN</span>
                    </div>

                    <p class="description">
                        Arjan is a desirable residential community in Dubai, merging modern conveniences with a strong sense
                        of neighborhood. Its welcoming atmosphere and high-quality developments attract those seeking an engaging
                        lifestyle. Arjan artfully combines luxury homes and amenities with a vibrant community spirit, offering
                        residents a perfect balance of refinement and connection.
                    </p>

                    <div class="apartment-details-grid">
                        <div class="detail-item">
                            <img src="./assets/images/bed.png" alt="Bedrooms">
                            <span class="bedrooms">3 Bedrooms</span>
                        </div>
                        <div class="detail-item">
                            <img src="./assets/images/bath.png" alt="Bathrooms">
                            <span class="bathrooms">1</span> Bathrooms
                        </div>
                        <div class="detail-item">
                            <img src="./assets/images/size.png" alt="Size">
                            <span class="size">1200 sqft</span>
                        </div>
                        <div class="detail-item">
                            <img src="./assets/images/type.png" alt="Type">
                            <span class="type">Apartment</span>
                        </div>
                    </div>

                </div>
                <div class="right-content">
                    <div class="info-box">
                        <div class="info-title amenities-title">Amenities</div>
                        <ul class="amenities-list">
                        </ul>

                        <!-- <div class="info-title" style="margin-top: 30px;">Location</div>
                        <ul class="location-list">
                            <li class="location-item"><span class="location-time">05 mins</span> <img src="./assets/images/arrow.svg" alt=""> Dubai Miracle Garden</span></li>
                            <li class="location-item"><span class="location-time">05 mins</span> <img src="./assets/images/arrow.svg" alt=""> Dubai Butterfly Garden</span></li>
                            <li class="location-item"><span class="location-time">10 mins</span> <img src="./assets/images/arrow.svg" alt=""> Dubai Hills Mall</span></li>
                            <li class="location-item"><span class="location-time">15 mins</span> <img src="./assets/images/arrow.svg" alt=""> Mall of the Emirates</span></li>
                            <li class="location-item"><span class="location-time">25 mins</span> <img src="./assets/images/arrow.svg" alt=""> Dubai International Airport</span></li>
                        </ul> -->
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="left-content">
                    <div class="prices">
                        <div class="price-label">Price</div>
                        <div class="price-aed">AED 1.100.000</div>
                    </div>
                </div>

            </div>

            <div class="content-grid">
                <div class="left-content">
                    <img src="./assets/images/logo-dark.svg" alt="Livrichy Real Estate Logo" class="logo" crossorigin="anonymous">
                </div>
                <div class="right-content">
                    <div class="agent-card">
                        <div class="agent-info">
                            <div class="agent-label">
                                <img src="./assets/images/agent.svg" alt="agent-icon" crossorigin="anonymous">
                                Agent
                            </div>
                            <div class="agent-name">SAMUEL SARPONG</div>
                            <div class="agent-contact">
                                <span class="agent-phone">+971 52 110 0555</span><br>
                                <span class="agent-email">samuel.s@livrichy.com</span>
                            </div>
                        </div>
                        <div class="agent-photo">
                            <img src="" class="agent-image" alt="Agent photo">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        async function generatePDF() {
            const {
                jsPDF
            } = window.jspdf;

            const wrapper = document.querySelector(".wrapper");
            if (!wrapper) {
                console.error("Wrapper element not found.");
                return;
            }

            // Replace images with base64
            await replaceImagesWithBase64(wrapper);

            // Wait for images to load
            await waitForImagesToLoad(wrapper);

            // Generate canvas using html2canvas
            const canvas = await html2canvas(wrapper, {
                useCORS: true
            });

            // Convert canvas to image data
            const imgData = canvas.toDataURL("image/png");

            // Create and configure the PDF
            const pdf = new jsPDF("p", "mm", [
                canvas.width * 0.264583,
                canvas.height * 0.264583,
            ]);
            pdf.addImage(
                imgData,
                "PNG",
                0,
                0,
                canvas.width * 0.264583,
                canvas.height * 0.264583
            );

            // Output the PDF
            const pdfBlob = pdf.output("blob");
            const pdfUrl = URL.createObjectURL(pdfBlob);
            window.open(pdfUrl, "_blank");
        }

        async function fetchImageAsBase64(url) {
            try {
                const response = await fetch(url, {
                    mode: "cors"
                });
                if (!response.ok)
                    throw new Error(`Failed to load image: ${response.status}`);
                const blob = await response.blob();
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.readAsDataURL(blob);
                });
            } catch (error) {
                console.error("Error fetching image:", error);
                return null;
            }
        }

        async function replaceImagesWithBase64(wrapper) {
            const images = Array.from(wrapper.querySelectorAll("img"));
            for (const img of images) {
                const base64Data = await fetchImageAsBase64(img.src);
                if (base64Data) {
                    img.src = base64Data;
                }
            }
        }

        async function waitForImagesToLoad(wrapper) {
            const images = Array.from(wrapper.querySelectorAll("img"));
            const imageLoadPromises = images.map((img) => {
                return new Promise((resolve) => {
                    if (img.complete) {
                        resolve();
                    } else {
                        img.onload = resolve;
                        img.onerror = resolve;
                    }
                });
            });
            await Promise.all(imageLoadPromises);
        }

        function formatPrice(price) {
            if (price === null || price === undefined) return null;
            if (isNaN(price)) return null;
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function getPropertyType(propertyType) {
            const types = {
                AP: "Apartment",
                BW: "Bungalow",
                CD: "Compound",
                DX: "Duplex",
                FF: "Full floor",
                HF: "Half floor",
                LP: "Land / Plot",
                PH: "Penthouse",
                TH: "Townhouse",
                VH: "Villa",
                WB: "Whole Building",
                HA: "Short Term / Hotel Apartment",
                LC: "Labor camp",
                BU: "Bulk units",
                WH: "Warehouse",
                FA: "Factory",
                OF: "Office space",
                RE: "Retail",
                SH: "Shop",
                SR: "Show Room",
                SA: "Staff Accommodation",
            };

            return types[propertyType] || propertyType;
        }

        function getAmenityName(shortCode) {
            const amenityMap = {
                'BA': 'Balcony',
                'BP': 'Basement parking',
                'BB': 'BBQ area',
                'AN': 'Cable-ready',
                'BW': 'Built in wardrobes',
                'CA': 'Carpets',
                'AC': 'Central air conditioning',
                'CP': 'Covered parking',
                'DR': 'Drivers room',
                'FF': 'Fully fitted kitchen',
                'GZ': 'Gazebo',
                'PY': 'Private Gym',
                'PJ': 'Jacuzzi',
                'BK': 'Kitchen Appliances',
                'MR': 'Maids Room',
                'MB': 'Marble floors',
                'HF': 'On high floor',
                'LF': 'On low floor',
                'MF': 'On mid floor',
                'PA': 'Pets allowed',
                'GA': 'Private garage',
                'PG': 'Garden',
                'PP': 'Swimming pool',
                'SA': 'Sauna',
                'SP': 'Shared swimming pool',
                'WF': 'Wood flooring',
                'SR': 'Steam room',
                'ST': 'Study',
                'UI': 'Upgraded interior',
                'GR': 'Garden view',
                'VW': 'Sea/Water view',
                'SE': 'Security',
                'MT': 'Maintenance',
                'IC': 'Within a Compound',
                'IS': 'Indoor swimming pool',
                'SF': 'Separate entrance for females',
                'BT': 'Basement',
                'SG': 'Storage room',
                'CV': 'Community view',
                'GV': 'Golf view',
                'CW': 'City view',
                'NO': 'North orientation',
                'SO': 'South orientation',
                'EO': 'East orientation',
                'WO': 'West orientation',
                'NS': 'Near school',
                'HO': 'Near hospital',
                'TR': 'Terrace',
                'NM': 'Near mosque',
                'SM': 'Near supermarket',
                'ML': 'Near mall',
                'PT': 'Near public transportation',
                'MO': 'Near metro',
                'VT': 'Near veterinary',
                'BC': 'Beach access',
                'PK': 'Public parks',
                'RT': 'Near restaurants',
                'NG': 'Near Golf',
                'AP': 'Near airport',
                'CS': 'Concierge Service',
                'SS': 'Spa',
                'SY': 'Shared Gym',
                'MS': 'Maid Service',
                'WC': 'Walk-in Closet',
                'HT': 'Heating',
                'GF': 'Ground floor',
                'SV': 'Server room',
                'DN': 'Pantry',
                'RA': 'Reception area',
                'VP': 'Visitors parking',
                'OP': 'Office partitions',
                'SH': 'Core and Shell',
                'CD': 'Children daycare',
                'CL': 'Cleaning services',
                'NH': 'Near Hotel',
                'CR': 'Conference room',
                'BL': 'View of Landmark',
                'PR': 'Children Play Area',
                'BH': 'Beach Access'
            };

            return amenityMap[shortCode] || shortCode;
        }


        document.addEventListener("DOMContentLoaded", async function() {
            const id = new URLSearchParams(window.location.search).get("id");
            const url =
                "https://crm.livrichy.com/rest/1509/hb3qi4ma9t11q1c4/crm.item.get?entityTypeId=1046&id=" +
                id;
            const response = await fetch(url);

            if (!response.ok) {
                console.error("Failed to fetch data:", response.status);
                return;
            }

            const data = await response.json();
            const property = data?.result?.item;

            // console.log(property);

            document.querySelector(".property-title").textContent = property.ufCrm13TitleEn;

            const city = property.ufCrm13City || property.ufCrm13BayutCity || "";
            const community = property.ufCrm13Community || property.ufCrm13BayutCommunity || "";
            const locationText = (city && community) ? `${city}, ${community}` : city || community || "Location not available";
            document.querySelector(".location-text").textContent = locationText.toUpperCase();

            document.querySelector(".price-aed").textContent = "AED " + formatPrice(property.ufCrm13Price);
            document.querySelector(".description").textContent = property.ufCrm13DescriptionEn.slice(0, 500) + "...";
            document.querySelector(".developer").textContent = property.ufCrm13Developers !== "" ? "By " + property.ufCrm13Developers : '';
            document.querySelector(".bathrooms").textContent = property.ufCrm13Bathroom;
            document.querySelector(".bedrooms").textContent = property.ufCrm13Bedroom == 0 ? "Studio" : property.ufCrm13Bedroom + " Bedrooms";
            document.querySelector(".type").textContent = getPropertyType(property.ufCrm13PropertyType);

            document.querySelector(".agent-name").textContent = property.ufCrm13AgentName;
            document.querySelector(".agent-email").textContent = property.ufCrm13AgentEmail;
            document.querySelector(".agent-phone").textContent = property.ufCrm13AgentPhone;

            document.querySelector(".main-image").src =
                property.ufCrm13PhotoLinks[0] + "?cache-bust=" + new Date().getTime();
            document.querySelector(".side-image-1").src =
                property.ufCrm13PhotoLinks[1] + "?cache-bust=" + new Date().getTime();
            document.querySelector(".side-image-2").src =
                property.ufCrm13PhotoLinks[2] + "?cache-bust=" + new Date().getTime();

            document.querySelector(".agent-image").src = property.ufCrm13AgentPhoto + "?cache-bust=" + new Date().getTime();

            const amenitiesList = document.querySelector(".amenities-list");
            if (property.ufCrm13Amenities.length === 0) {
                document.querySelector(".info-box").style.display = "none";
            }
            property.ufCrm13Amenities.forEach((amenity, index) => {
                if (index > 7) return;

                const li = document.createElement("li");
                li.classList.add("amenity-item");
                li.textContent = "— " + getAmenityName(amenity);
                amenitiesList.appendChild(li);
            })

            //await generatePDF();

            //window.location.href = "index.php";
        });
    </script>
</body>

</html>