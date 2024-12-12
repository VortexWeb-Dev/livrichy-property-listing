<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Livrichy Property Listing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
</head>

<body class="bg-gray-100 p-6 overflow-y-auto">
    <div
        class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden"
        id="brochure-card">
        <div class="relative bg-yellow-500">
            <img
                src="placeholder.jpeg"
                alt="Villa"
                class="w-full h-96 object-cover" id="imageLarge" />
            <div class="absolute top-0 left-0 p-4 bg-yellow-500 text-white">
                <h1 class="text-3xl font-bold">LIVRICHY REAL ESTATE</h1>
            </div>
        </div>
        <div class="p-6">
            <h2 class="text-4xl font-bold text-yellow-500 uppercase" id="title">
                Single Row Villa
            </h2>
            <p class="text-xl text-gray-700">
                Offered at
                <span class="font-bold text-yellow-500" id="priceText">AED <span id="price"></span></span>
            </p>
        </div>
        <div class="px-6">
            <h3 class="text-2xl font-bold" id="subtitle">Villa for rent in Al Furjan</h3>
            <p class="text-gray-700 mt-4" id="description">
                Brand-new 4-bedroom + maid's villa in Murooj East, Al Furjan.
                Single-row, park-facing, private garden, closed kitchen, spacious
                living area, 3 parking spaces. Gated community with pool, courts, play
                areas, parks, and retail. Near Sheikh Zayed Road for easy access to
                key Dubai areas.
            </p>
        </div>
        <div class="p-6">
            <h3 class="text-2xl font-bold text-gray-800">Our Facilities</h3>
            <ul class="list-disc list-inside text-gray-700 mt-2">
                <li id="propertyType">Villa</li>
                <li><span id="size"></span> sqft / <span id="sizeSqm"></span> sqm</li>
                <li><span id="bathrooms"></span> Bathrooms</li>
                <li><span id="bedrooms"></span> Bedrooms</li>
            </ul>
        </div>
        <div class="grid grid-cols-3 gap-4 px-6">
            <img
                src="placeholder.jpeg"
                alt="Interior 1"
                class="w-full h-40 object-cover rounded-lg"
                id="image1" />
            <img
                src="placeholder.jpeg"
                alt="Interior 2"
                class="w-full h-40 object-cover rounded-lg"
                id="image2" />
            <img
                src="placeholder.jpeg"
                alt="Interior 3"
                class="w-full h-40 object-cover rounded-lg"
                id="image3" />
        </div>
        <div class="w-full bg-gray-200 p-6 mt-6 flex justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Contact Us</h3>
                <p class="text-xl text-yellow-500 font-bold mt-2">+971 52 110 0555</p>
                <p class="text-gray-700">Quick Respond</p>
            </div>
            <div class="flex items-center mt-4">
                <img
                    src="placeholder.jpeg"
                    alt="Sachin Das"
                    class="w-16 h-16 rounded-full object-cover" id="agentImage" />

                <div class="ml-4">
                    <p class="font-bold text-gray-800" id="agentName">Sachin Das</p>
                    <p class="text-gray-700" id="agentPhone">+971 50 591 5264</p>
                    <p class="text-gray-700" id="agentEmail">2Zi0d@example.com</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        async function downloadBrochure(filename) {
            const {
                jsPDF
            } = window.jspdf;

            const doc = new jsPDF("p", "mm", "a4");
            const brochureElement = document.getElementById("brochure-card");

            html2canvas(brochureElement, {
                scale: 2,
                useCORS: true,
                logging: true,
                allowTaint: true,
                backgroundColor: null,
            }).then(function(canvas) {
                const imgData = canvas.toDataURL("image/png");

                const imgWidth = 210;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                doc.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);

                doc.save(filename + ".pdf");
            }).catch(function(error) {
                console.error("Error generating brochure PDF:", error);
            });

            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    resolve();
                }, 2000);
            });
        }

        function sqftToSqm(sqft) {
            return Math.round(sqft / 0.092903);
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
                SA: "Staff Accommodation"
            }

            return types[propertyType];
        }

        function getOfferingType(offeringType) {
            const types = {
                "RS": "Sale",
                "CS": "Sale",
                "RR": "Rent",
                "CR": "Rent",
            }

            return types[offeringType] || "Sale";
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('en-US').format(price);
        }

        async function fetchPropertyDetails(propertyId) {
            const response = await fetch(`https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4/crm.item.get?entityTypeId=1046&id=${propertyId}`);
            const data = await response.json();
            if (!data.result) throw new Error("Property not found.");
            return data.result.item;
        }

        async function populateBrochureContent(property) {

            document.getElementById("title").textContent = property["ufCrm13TitleEn"] || "Property Title Not Available";
            document.getElementById("description").textContent = property["ufCrm13BrochureDescription"] || (property["ufCrm13DescriptionEn"]?.slice(0, 380) + "...");
            document.getElementById("size").textContent = property["ufCrm13Size"] || "Size Not Available";
            document.getElementById("sizeSqm").textContent = sqftToSqm(property["ufCrm13Size"]) || "Size Not Available";
            document.getElementById("bathrooms").textContent = property["ufCrm13Bathroom"] || "N/A";
            document.getElementById("bedrooms").textContent = property["ufCrm13Bedroom"] || "N/A";
            document.getElementById("propertyType").textContent = getPropertyType(property["ufCrm13PropertyType"]) || "Type Not Available";
            document.getElementById("price").textContent = formatPrice(property["ufCrm13Price"]) || "Price Not Available";

            // Price text
            let priceText = getPriceText(property);
            document.getElementById("priceText").textContent = priceText;

            // Subtitle
            const subtitle = `${getPropertyType(property["ufCrm13PropertyType"])} for ${getOfferingType(property["ufCrm13OfferingType"])} in ${property["ufCrm13Community"] || "N/A"}`;
            document.getElementById("subtitle").textContent = subtitle;

            // Agent info
            document.getElementById("agentName").textContent = property["ufCrm13AgentName"] || "Agent Not Available";
            document.getElementById("agentPhone").textContent = property["ufCrm13AgentPhone"] || "Phone Not Available";
            document.getElementById("agentEmail").textContent = property["ufCrm13AgentEmail"] || "Email Not Available";
            document.getElementById("agentImage").src = property["ufCrm13AgentPhoto"] || "https://via.placeholder.com/150";

            // Images
            setImages(property["ufCrm13PhotoLinks"]);

            return new Promise((resolve, reject) => {
                // Simulate a delay or async work if needed
                setTimeout(() => {
                    resolve(); // Resolve when brochure content is fully populated
                }, 1000); // You can adjust the timeout or remove it based on your actual async work
            });
        }

        function setImages(imageLinks) {
            if (imageLinks && imageLinks.length > 0) {
                document.getElementById("imageLarge").src = imageLinks[0];
                document.getElementById("image1").src = imageLinks[1];
                document.getElementById("image2").src = imageLinks[2];
                document.getElementById("image3").src = imageLinks[3];
            }
        }

        function getPriceText(property) {
            let priceText = " AED " + formatPrice(property["ufCrm13Price"]);

            if (property["ufCrm13RentalPeriod"] === 'Y') {
                priceText = `AED ${formatPrice(property["ufCrm13YearlyPrice"])} /year`;
            } else if (property["ufCrm13RentalPeriod"] === 'M') {
                priceText = `AED ${formatPrice(property["ufCrm13MonthlyPrice"])} /month`;
            } else if (property["ufCrm13RentalPeriod"] === 'D') {
                priceText = `AED ${formatPrice(property["ufCrm13DailyPrice"])} /day`;
            } else if (property["ufCrm13RentalPeriod"] === 'W') {
                priceText = `AED ${formatPrice(property["ufCrm13WeeklyPrice"])} /week`;
            }

            return priceText;
        }

        document.addEventListener("DOMContentLoaded", async function() {
            try {
                const propertyId = <?php echo $_GET['id']; ?>;
                const property = await fetchPropertyDetails(propertyId);
                await populateBrochureContent(property);
                await downloadBrochure(property["ufCrm13TitleEn"]);
                window.location.href = "index.php";
            } catch (error) {
                console.error("Error:", error);
            }
        });
    </script>
</body>

</html>