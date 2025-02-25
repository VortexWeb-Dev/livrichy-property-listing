<?php
require_once('./utils/index.php');
$current_user = fetchCurrentUser();

$agent_name = $current_user['NAME'] . ' ' . $current_user['LAST_NAME'];
$agent_phone = $current_user['PERSONAL_MOBILE'];
$agent_photo = $current_user['PERSONAL_PHOTO'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style type="text/css">
    body {
      margin: 0 auto;
      padding: 0;
      width: 900px;
      /* height: 1200px; */
      background-color: #ffffff;
      font-family: Arial, sans-serif;
    }

    img {
      object-fit: cover;
    }

    .wrapper {
      position: relative;
      width: 100%;
      height: 100%;
    }

    .main-container {
      position: relative;
      width: 100%;
      height: 500px;
    }

    .main-image-container {
      position: relative;
      width: 100%;
      height: 500px;
    }

    .main-image {
      width: 100%;
      height: 100%;
    }

    .qr-code {
      position: absolute;
      left: 20px;
      top: 20px;
      height: 80px;
      width: 80px;
    }

    .logo-container {
      position: absolute;
      top: 0;
      right: 70px;
      width: 300px;
      height: 150px;
      background-color: #b7a05c;
    }

    .logo {
      width: 200px;
      /* height: 100px; */
      margin: 50px;
    }

    .content-section {
      position: relative;
      margin: 25px;
      padding-top: 20px;
    }

    .left-content {
      width: 500px;
      float: left;
    }

    .right-content {
      position: absolute;
      top: 0;
      right: 0;
      width: 300px;
      background-color: #b7a05c;
      padding: 20px;
      color: white;
    }

    .facilities-table {
      width: 100%;
      margin-top: 20px;
    }

    .facilities-table td {
      padding: 5px 0;
    }

    .gallery-section {
      display: flex;
      text-align: center;
      margin: 25px;
    }

    .gallery-image {
      width: 32%;
      margin-right: 1%;
      border-radius: 28px;
    }

    .contact-section {
      position: relative;
      margin: 25px;
      height: 150px;
    }

    .contact-info {
      float: left;
      width: 50%;
    }

    .agent-info {
      position: absolute;
      right: 0;
      top: 0;
      width: 250px;
      background-color: #b7a05c;
      padding: 10px;
      color: white;
    }

    .agent-photo {
      float: left;
      height: 60px;
      width: 60px;
      margin-right: 10px;
      border-radius: 50%;
    }

    .agent-details {
      margin-left: 70px;
    }

    .clearfix:after {
      content: "";
      display: table;
      clear: both;
    }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <div class="main-container">
      <div class="main-image-container">
        <img
          id="mainImage"
          crossorigin="anonymous"
          src=""
          alt="Property"
          class="main-image" />
      </div>
      <img
        crossorigin="anonymous"
        src="https://quickchart.io/chart?cht=qr&chs=300x300&chl=https://livrichy.com/"
        alt="QR"
        class="qr-code" />
      <div class="logo-container">
        <img
          crossorigin="anonymous"
          src="https://livrichy.s3.ap-south-1.amazonaws.com/logo.png"
          alt="Logo"
          class="logo" />
      </div>
    </div>

    <div class="content-section clearfix">
      <div class="left-content">
        <h2 id="title"></h2>
        <p id="description"></p>
        <h3>Our Facilities</h3>
        <table class="facilities-table">
          <tr>
            <td>
              <img
                style="margin-right: 10px"
                src="assets/images/type.png" /><span id="propertyType"></span>
            </td>
            <td>
              <img
                style="margin-right: 10px"
                src="assets/images/size.png"
                alt="" />
              <span id="size"></span> sqft / <span id="sizeSqm"></span> sqm
            </td>
          </tr>
          <tr>
            <td>
              <img style="margin-right: 10px" src="assets/images/bath.png" />
              <span id="bathrooms"></span> Bathrooms
            </td>
            <td>
              <img style="margin-right: 10px" src="assets/images/bed.png" />
              <span id="bedrooms"></span> Bedrooms
            </td>
          </tr>
        </table>
      </div>
      <div class="right-content">
        Offered at
        <div id="priceText"></div>
      </div>
    </div>

    <div class="gallery-section">
      <img
        id="image1"
        crossorigin="anonymous"
        src=""
        alt="Gallery Image 1"
        class="gallery-image" />
      <img
        id="image2"
        crossorigin="anonymous"
        src=""
        alt="Gallery Image 2"
        class="gallery-image" />
      <img
        id="image3"
        crossorigin="anonymous"
        src=""
        alt="Gallery Image 3"
        class="gallery-image" />
    </div>

    <div class="contact-section clearfix">
      <div class="contact-info">
        <p style="font-weight: bold; font-size: 10pt">Contact Us</p>
        <h2 style="font-weight: bold; font-size: 14pt">
          <b>+971 52110 0555</b>
        </h2>
        <span>Quick Respond</span>
      </div>
      <div class="agent-info">
        <img
          id="agentPhoto"
          crossorigin="anonymous"
          crossorigin="anonymous"
          src=""
          alt="Agent"
          class="agent-photo" />
        <div class="agent-details">
          <b id="agentName"></b>
          <p id="agentPhone"></p>
        </div>
      </div>
    </div>
  </div>

  <script>
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

    function sizeSqm(size) {
      return (size / 10.7639).toFixed(2);
    }

    function formatPrice(price) {
      if (price === null || price === undefined) return null;
      if (isNaN(price)) return null;
      return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function getPriceText(property) {
      if (!property) return "Price not available";

      const rentalPeriodMapping = {
        Y: "YearlyPrice",
        M: "MonthlyPrice",
        D: "DailyPrice",
        W: "WeeklyPrice",
      };

      const rentalKey = rentalPeriodMapping[property["ufCrm13RentalPeriod"]];
      const rentalPrice =
        rentalKey && property[`ufCrm13${rentalKey}`] !== null && property[`ufCrm13${rentalKey}`] !== 0 ?
        property[`ufCrm13${rentalKey}`] :
        property["ufCrm13Price"];

      const formattedPrice = formatPrice(rentalPrice);
      const periodText = rentalKey ? ` /${rentalKey[0].toLowerCase()}` : "";

      console.log(rentalKey, rentalPrice, formattedPrice, periodText);

      return formattedPrice ?
        `AED ${formattedPrice}${periodText}` :
        formatPrice(property["ufCrm13Price"]);
    }

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

      console.log(property);

      document.getElementById("title").textContent = property.ufCrm13TitleEn;
      document.getElementById("description").textContent =
        property.ufCrm13BrochureDescription && property.ufCrm13BrochureDescription !== "null" ? property.ufCrm13BrochureDescription :
        property.ufCrm13DescriptionEn.slice(0, 200);
      document.getElementById("propertyType").textContent = getPropertyType(
        property.ufCrm13PropertyType
      );
      document.getElementById("bedrooms").textContent =
        property.ufCrm13Bedroom;
      document.getElementById("bathrooms").textContent =
        property.ufCrm13Bathroom;
      document.getElementById("size").textContent = property.ufCrm13Size;
      document.getElementById("sizeSqm").textContent = sizeSqm(
        property.ufCrm13Size
      );
      document.getElementById("agentName").textContent =
        <?php echo json_encode($agent_name) ?>;
      document.getElementById("agentPhone").textContent =
        <?php echo json_encode($agent_phone) ?>;

      document.getElementById("priceText").textContent =
        getPriceText(property);

      document.getElementById("agentPhoto").src = <?php echo json_encode($agent_photo); ?> + "?cache-bust=12345";

      document.getElementById("mainImage").src =
        property.ufCrm13PhotoLinks[0] + "?cache-bust=12345";
      document.getElementById("image1").src =
        property.ufCrm13PhotoLinks[1] + "?cache-bust=12345";
      document.getElementById("image2").src =
        property.ufCrm13PhotoLinks[2] + "?cache-bust=12345";
      document.getElementById("image3").src =
        property.ufCrm13PhotoLinks[3] + "?cache-bust=12345";

      await generatePDF();

      window.location.href = "index.php";
    });
  </script>
</body>

</html>