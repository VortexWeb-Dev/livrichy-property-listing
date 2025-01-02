<?php
require 'vendor/autoload.php';
require 'crest/crest.php';
require 'crest/settings.php';

use Mpdf\Mpdf;

function sqftToSqm($sqft)
{
    return round(($sqft * 0.09290304), 2);
}

function getPropertyType($propertyType)
{
    $types = [
        "AP" => "Apartment",
        "BW" => "Bungalow",
        "CD" => "Compound",
        "DX" => "Duplex",
        "FF" => "Full floor",
        "HF" => "Half floor",
        "LP" => "Land / Plot",
        "PH" => "Penthouse",
        "TH" => "Townhouse",
        "VH" => "Villa",
        "WB" => "Whole Building",
        "HA" => "Short Term / Hotel Apartment",
        "LC" => "Labor camp",
        "BU" => "Bulk units",
        "WH" => "Warehouse",
        "FA" => "Factory",
        "OF" => "Office space",
        "RE" => "Retail",
        "SH" => "Shop",
        "SR" => "Show Room",
        "SA" => "Staff Accommodation"
    ];

    return $types[$propertyType];
}

function getOfferingType($offeringType)
{
    $types = [
        "RS" => "Sale",
        "CS" => "Sale",
        "RR" => "Rent",
        "CR" => "Rent",
    ];

    return $types[$offeringType] || "Sale";
}

function formatPrice($price)
{
    return number_format($price, 2);
}

function getPriceText($property)
{
    $priceText = "AED " . formatPrice($property["ufCrm13Price"]);

    if ($property["ufCrm13RentalPeriod"] === 'Y') {
        $priceText = "AED " . formatPrice($property['ufCrm13YearlyPrice']) . " /year";
    } else if ($property["ufCrm13RentalPeriod"] === 'M') {
        $priceText = "AED " . formatPrice($property['ufCrm13MonthlyPrice']) . " /month";
    } else if ($property["ufCrm13RentalPeriod"] === 'D') {
        $priceText = "AED " . formatPrice($property['ufCrm13DailyPrice']) . " /day";
    } else if ($property["ufCrm13RentalPeriod"] === 'W') {
        $priceText = "AED " . formatPrice($property['ufCrm13WeeklyPrice']) . " /week";
    }

    return $priceText;
}


try {

    $property_id = $_GET['id'];
    $result = CRest::call('crm.item.get', ['entityTypeId' => LISTINGS_ENTITY_TYPE_ID, 'id' => $property_id]);
    $property = $result['result']['item'];


    $tempDir = __DIR__ . '/tmp';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $mpdfConfig = [
        'tempDir' => $tempDir,
    ];

    $mpdf = new Mpdf($mpdfConfig);


    $htmlTemplate = file_get_contents('template.html');


    $data = [
        '{{title}}' => $property['ufCrm13TitleEn'] ?? 'N/A',
        '{{description}}' => substr($property['ufCrm13DescriptionEn'], 0, 500) ?? 'N/A',
        '{{size}}' => $property['ufCrm13Size'] ?? '0',
        '{{sizeSqm}}' => sqftToSqm($property['ufCrm13Size'] ?? 0),
        '{{bathrooms}}' => $property['ufCrm13Bathroom'] ?? '0',
        '{{bedrooms}}' => $property['ufCrm13Bedroom'] ?? '0',
        '{{propertyType}}' => getPropertyType($property['ufCrm13PropertyType'] ?? 'N/A'),
        '{{priceText}}' => getPriceText($property),
        '{{agentName}}' => $property['ufCrm13AgentName'] ?? 'N/A',
        '{{agentPhone}}' => $property['ufCrm13AgentPhone'] ?? 'N/A',
        '{{agentEmail}}' => $property['ufCrm13AgentEmail'] ?? 'N/A',
        '{{agentPhoto}}' => $property['ufCrm13AgentPhoto'] ?? 'https://via.placeholder.com/150',
        '{{mainImage}}' => $property['ufCrm13PhotoLinks'][0] ?? 'https://via.placeholder.com/150',
        '{{image1}}' => $property['ufCrm13PhotoLinks'][1] ?? 'https://via.placeholder.com/150',
        '{{image2}}' => $property['ufCrm13PhotoLinks'][2] ?? 'https://via.placeholder.com/150',
        '{{image3}}' => $property['ufCrm13PhotoLinks'][3] ?? 'https://via.placeholder.com/150',
    ];

    foreach ($data as $placeholder => $value) {
        $htmlTemplate = str_replace($placeholder, $value, $htmlTemplate);
    }


    $mpdf->WriteHTML($htmlTemplate);

    function sanitizeFilename($filename)
    {
        $filename = str_replace(' ', '_', $filename);
        $filename = preg_replace('/[^A-Za-z0-9\-]/', '', $filename);
        return $filename;
    }

    $mpdf->Output(sanitizeFilename($property['ufCrm13TitleEn']) . '.pdf', 'D');
} catch (\Mpdf\MpdfException $e) {
    echo 'Error creating PDF: ' . $e->getMessage();
}
