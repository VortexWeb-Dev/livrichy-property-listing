<?php
require 'utils/index.php';

header('Content-Type: application/json; charset=UTF-8');

$baseUrl = 'https://crm.livrichy.com/rest/1509/o8fnjtg7tyf787h4';
$entityTypeId = 1046;
$fields = [
    'id',
    'ufCrm13ReferenceNumber',
    'ufCrm13PermitNumber',
    'ufCrm13ReraPermitNumber',
    'ufCrm13DtcmPermitNumber',
    'ufCrm13OfferingType',
    'ufCrm13PropertyType',
    'ufCrm13HidePrice',
    'ufCrm13RentalPeriod',
    'ufCrm13YearlyPrice',
    'ufCrm13MonthlyPrice',
    'ufCrm13WeeklyPrice',
    'ufCrm13DailyPrice',
    'ufCrm13Price',
    'ufCrm13ServiceCharge',
    'ufCrm13NoOfCheques',
    'ufCrm13City',
    'ufCrm13Community',
    'ufCrm13SubCommunity',
    'ufCrm13Tower',
    'ufCrm13TitleEn',
    'ufCrm13TitleAr',
    'ufCrm13DescriptionEn',
    'ufCrm13DescriptionAr',
    'ufCrm13TotalPlotSize',
    'ufCrm13Size',
    'ufCrm13Bedroom',
    'ufCrm13Bathroom',
    'ufCrm13AgentId',
    'ufCrm13AgentName',
    'ufCrm13AgentEmail',
    'ufCrm13AgentPhone',
    'ufCrm13AgentPhoto',
    'ufCrm13BuildYear',
    'ufCrm13Parking',
    'ufCrm13Furnished',
    'ufCrm_13_360_VIEW_URL',
    'ufCrm13PhotoLinks',
    'ufCrm13FloorPlan',
    'ufCrm13Geopoints',
    'ufCrm13Latitude',
    'ufCrm13Longitude',
    'ufCrm13AvailableFrom',
    'ufCrm13VideoTourUrl',
    'ufCrm13Developers',
    'ufCrm13ProjectName',
    'ufCrm13ProjectStatus',
    'ufCrm13ListingOwner',
    'ufCrm13Status',
    'ufCrm13PfEnable',
    'ufCrm13BayutEnable',
    'ufCrm13DubizzleEnable',
    'ufCrm13WebsiteEnable',
    'updatedTime'
];

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields,);

if (count($properties) > 0) {
    $json = generateWebsiteJson($properties);
    echo $json;
} else {
    echo json_encode([]);
}
