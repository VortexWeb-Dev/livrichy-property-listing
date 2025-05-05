<?php

require_once(__DIR__ . "/../crest/crest.php");
require_once(__DIR__ . "/../crest/crestcurrent.php");

$images_to_skip = [
    "LIV-S-TheField-AInew2",
    "LIV-S-MarinaVista04",
    "LIV-S-RedSqaureFA04",
    "LIV-S-KempinskiBJ1",
    "LIV-S-PL01MD",
    "LIV-S-DCTB06MB",
    "LIV-S-AYC3TDMB",
    "livrichy-real-estate-brokerage-11016445",
    "livrichy-real-estate-brokerage-11388149",
    "LIV-S-Peninsula29",
    "LIV-S-TheField-AInew1",
    "LIV-S-GOLF-S",
    "LIV-S-Terrace2BR",
    "LIV-S-TMD04K",
    "LIV-S-ERIN",
    "LIV-S-OCEANCove",
    "LIV-S-MajaraSD",
    "LIV-R-MiraclzSD2",
    "livrichy-real-estate-brokerage-11348107",
    "livrichy-real-estate-brokerage-11354989",
    "livrichy-real-estate-brokerage-11346061",
    "LIV-S-SERENA-KSV",
    "livrichy-real-estate-brokerage-10677740",
    "livrichy-real-estate-brokerage-11390606",
    "livrichy-real-estate-brokerage-11136714",
    "LIV-R-SLS-11109877",
    "livrichy-real-estate-brokerage-11305886",
    "LIV-R-SLS2-SAM",
    "LIV-R-SLS1-SAM",
    "LIV-R-SLS-SAM4",
    "livrichy-real-estate-brokerage-10842284",
    "LIV-S-CENTRALPARK1",
    "LIV-R-Icon17-SR",
    "LIV-S-GROVE-MARYNA",
    "livrichy-real-estate-brokerage-10958640",
    "LIV-S-SLS17VD",
    "LIV-R-SLSVD",
    "livrichy-real-estate-brokerage-11366156",
    "livrichy-real-estate-brokerage-11017220",
    "livrichy-real-estate-brokerage-11109915",
    "LIV-S-Safa2MarynaM1",
    "livrichy-real-estate-brokerage-10886896",
    "livrichy-real-estate-brokerage-10655509",
    "livrichy-real-estate-brokerage-11256806",
    "LIV-S-MuroojZ",
    "LIV-S-DQ3-AK",
    "LIV-S-DQ5-AK",
    "LIV-S-JGT2EK",
    "LIV-S-MBL09BJ",
    "LIV-S-RUKAN-KS",
    "livrichy-real-estate-brokerage-11274942",
    "LIV-S-SAFA2BJ",
    "LIV-S-SapphireBJrenew",
    "LIV-S-PalmBeachBJ04",
    "LIV-S-MBL08BJ",
    "LIV-S-Crescent05BJ1BED",
    "LIV-S-Crestcent2BEDBJ",
    "LIV-S-PearlEK",
    "LIV-S-PeninsulaMB",
    "LIV-S-MQWSD",
    "LIV-S-Nautica1M",
    "LIV-S-TheCrestCM",
    "LIV-S-TheCrestIM",
    "LIV-R-ACACIA-RL",
    "LIV-S-HarringtonRL",
    "LIV-S-MarinaVistanew",
    "livrichy-real-estate-brokerage-11256541",
    "livrichy-real-estate-brokerage-10774462",
    "livrichy-real-estate-brokerage-11243910",
    "LIV-S-306 SD",
    "livrichy-real-estate-brokerage-11261810",
    "LIV-S-Sobhavista04",
    "LIV-S-ParkHeights04",
    "LIV-S-Burjkhalifa",
    "livrichy-real-estate-brokerage-11006548",
    "LIV-S-North43LA",
    "LIV-S-ElevateET",
    "LIV-S-Aykon3LA",
    "LIV-S-MarbellaLT",
    "ALJurf-013",
    "LIV-S-AddressBay07",
    "livrichy-real-estate-brokerage-10963824",
    "LIV-S-Kempinski1BRK",
    "LIV-S-DQ08NEW",
    "LIV-S-DQ01NEW",
    "LIV-S-DQ7NEW",
    "LIV-S-GroveEKnew",
    "LIV-S-YasGolfEKnew",
    "LIV-S-SGardensSBnew",
    "LIV-S-OceanoEkaterinanew",
    "LIV-S-Murjansimon",
    "LIV-S-WestwoodK",
    "LIV-S-AddressBay08",
    "LIV-S-TORCHSNEW",
    "LIV-S-AddressBay06",
    "LIV-S-AddressBay05",
    "livrichy-real-estate-brokerage-11046843",
    "LIV-S-AsayelMJ",
    "LIV-S-SafaTwoBJ",
    "LIV-S-GH08K",
    "LIV-S-Fern05K",
    "LIV-R-MQWSD",
    "LIV-S-GH04K",
    "LIV-S-RixosK",
    "LIV-S-GH05K",
    "LIV-R-StudioOneK",
    "LIV-S-GH06K",
    "LIV-S-RedSqaureAE",
    "LIV-S-5103F",
    "LIV-S-2911F",
    "LIV-S-2301HA",
    "LIV-S-Views03LA",
    "LIV-S-3003GH",
    "LIV-S-264Ekaterinarenew",
    "LIV-R-0606Simon",
    "livrichy-real-estate-brokerage-11005456",
    "LIV-S-BEACHouse",
    "LIV-S-AMWAJ-AE",
    "LIV-S-1002H",
    "LIV-S-4006ilona",
    "LIV-S-4404ilona",
    "LIV-S-144ilona",
    "LIV-S-1002ilona",
    "LIV-S-301ilona",
    "LIV-S-BB01IM",
    "LIV-S-ThymeIlona",
    "LIV-S-Lamaa03",
    "LIV-S-GH07K",
    "LIV-S-Lamaa09",
    "LIV-S-AlJaziBJ",
    "LIV-S-ParkH",
    "LIV-S-1310F",
    "LIV-S-RegaliaMN",
    "LIV-S-MarinavistaRuby",
    "LIV-S-SkyBayH11",
];


function buildApiUrl($baseUrl, $entityTypeId, $fields, $start = 0)
{
    $selectParams = '';
    foreach ($fields as $index => $field) {
        $selectParams .= "select[$index]=$field&";
    }
    $selectParams = rtrim($selectParams, '&');
    return "$baseUrl/crm.item.list?entityTypeId=$entityTypeId&$selectParams&start=$start&filter[ufCrm13Status]=PUBLISHED";
}

function fetchAllProperties($baseUrl, $entityTypeId, $fields, $platform = null)
{
    $allProperties = [];
    $start = 0;

    try {
        while (true) {
            $apiUrl = buildApiUrl($baseUrl, $entityTypeId, $fields, $start);
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if (isset($data['result']['items'])) {
                $properties = $data['result']['items'];
                $allProperties = array_merge($allProperties, $properties);
            }

            // If there's no "next" key, we've fetched all data
            if (empty($data['next'])) {
                break;
            }

            $start = $data['next'];
        }

        if ($platform) {
            switch ($platform) {
                case 'pf':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm13PfEnable'] === 'Y';
                    });
                    break;
                case 'bayut':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm13BayutEnable'] === 'Y';
                    });
                    break;
                case 'dubizzle':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm13DubizzleEnable'] === 'Y';
                    });
                    break;
                case 'website':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm13WebsiteEnable'] === 'Y';
                    });
                    break;
                case 'active':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm13Status'] === 'PUBLISHED' || $property['ufCrm13Status'] === 'POCKET';
                    });
                    break;
                default:
                    break;
            }
        }

        return $allProperties;
    } catch (Exception $e) {
        error_log('Error fetching properties: ' . $e->getMessage());
        return [];
    }
}

function getPropertyPurpose($property)
{
    return ($property['ufCrm13OfferingType'] == 'RR' || $property['ufCrm13OfferingType'] == 'CR') ? 'Rent' : 'Buy';
}

function getPropertyType($property)
{
    $property_types = array(
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
        "LP" => "Plot",
        "SH" => "Shop",
        "SR" => "Show Room",
        "SA" => "Staff Accommodation"
    );

    return $property_types[$property['ufCrm13PropertyType']] ?? '';
}

function getPermitNumber($property)
{
    if (!empty($property['ufCrm13PermitNumber'])) {
        return $property['ufCrm13PermitNumber'];
    }
    return $property['ufCrm13ReraPermitNumber'] ?? '';
}

function getFullAmenityName($shortCode)
{
    $amenityMap = [
        'BA' => 'Balcony',
        'BP' => 'Basement parking',
        'BB' => 'BBQ area',
        'AN' => 'Cable-ready',
        'BW' => 'Built in wardrobes',
        'CA' => 'Carpets',
        'AC' => 'Central air conditioning',
        'CP' => 'Covered parking',
        'DR' => 'Drivers room',
        'FF' => 'Fully fitted kitchen',
        'GZ' => 'Gazebo',
        'PY' => 'Private Gym',
        'PJ' => 'Jacuzzi',
        'BK' => 'Kitchen Appliances',
        'MR' => 'Maids Room',
        'MB' => 'Marble floors',
        'HF' => 'On high floor',
        'LF' => 'On low floor',
        'MF' => 'On mid floor',
        'PA' => 'Pets allowed',
        'GA' => 'Private garage',
        'PG' => 'Garden',
        'PP' => 'Swimming pool',
        'SA' => 'Sauna',
        'SP' => 'Shared swimming pool',
        'WF' => 'Wood flooring',
        'SR' => 'Steam room',
        'ST' => 'Study',
        'UI' => 'Upgraded interior',
        'GR' => 'Garden view',
        'VW' => 'Sea/Water view',
        'SE' => 'Security',
        'MT' => 'Maintenance',
        'IC' => 'Within a Compound',
        'IS' => 'Indoor swimming pool',
        'SF' => 'Separate entrance for females',
        'BT' => 'Basement',
        'SG' => 'Storage room',
        'CV' => 'Community view',
        'GV' => 'Golf view',
        'CW' => 'City view',
        'NO' => 'North orientation',
        'SO' => 'South orientation',
        'EO' => 'East orientation',
        'WO' => 'West orientation',
        'NS' => 'Near school',
        'HO' => 'Near hospital',
        'TR' => 'Terrace',
        'NM' => 'Near mosque',
        'SM' => 'Near supermarket',
        'ML' => 'Near mall',
        'PT' => 'Near public transportation',
        'MO' => 'Near metro',
        'VT' => 'Near veterinary',
        'BC' => 'Beach access',
        'PK' => 'Public parks',
        'RT' => 'Near restaurants',
        'NG' => 'Near Golf',
        'AP' => 'Near airport',
        'CS' => 'Concierge Service',
        'SS' => 'Spa',
        'SY' => 'Shared Gym',
        'MS' => 'Maid Service',
        'WC' => 'Walk-in Closet',
        'HT' => 'Heating',
        'GF' => 'Ground floor',
        'SV' => 'Server room',
        'DN' => 'Pantry',
        'RA' => 'Reception area',
        'VP' => 'Visitors parking',
        'OP' => 'Office partitions',
        'SH' => 'Core and Shell',
        'CD' => 'Children daycare',
        'CL' => 'Cleaning services',
        'NH' => 'Near Hotel',
        'CR' => 'Conference room',
        'BL' => 'View of Landmark',
        'PR' => 'Children Play Area',
        'BH' => 'Beach Access'
    ];

    return $amenityMap[$shortCode] ?? $shortCode;
}

function formatDate($date)
{
    return $date ? date('Y-m-d H:i:s', strtotime($date)) : date('Y-m-d H:i:s');
}

function formatField($field, $value, $type = 'string')
{
    if (empty($value) && $value != 0) {
        return '';
    }

    switch ($type) {
        case 'date':
            return '<' . $field . '>' . formatDate($value) . '</' . $field . '>';
        default:
            return '<' . $field . '>' . htmlspecialchars($value) . '</' . $field . '>';
    }
}

function formatPriceOnApplication($property)
{
    $priceOnApplication = ($property['ufCrm13HidePrice'] === 'Y') ? 'Yes' : 'No';
    return formatField('price_on_application', $priceOnApplication);
}

function formatRentalPrice($property)
{
    if (empty($property['ufCrm13RentalPeriod'])) {
        return formatField('price', $property['ufCrm13Price']);
    }

    switch ($property['ufCrm13RentalPeriod']) {
        case 'Y':
            return formatField('price', $property['ufCrm13Price'], 'yearly');
        case 'M':
            return formatField('price', $property['ufCrm13Price'], 'monthly');
        case 'W':
            return formatField('price', $property['ufCrm13Price'], 'weekly');
        case 'D':
            return formatField('price', $property['ufCrm13Price'], 'daily');
        default:
            return formatField('price', $property['ufCrm13Price']);
    }
}

function formatBedroom($property)
{
    return formatField('bedroom', ($property['ufCrm13Bedroom'] > 7) ? '7+' : $property['ufCrm13Bedroom']);
}

function formatBathroom($property)
{
    return formatField('bathroom', ($property['ufCrm13Bathroom'] > 7) ? '7+' : $property['ufCrm13Bathroom']);
}

function formatFurnished($property)
{
    $furnished = $property['ufCrm13Furnished'] ?? '';
    if ($furnished) {
        switch ($furnished) {
            case 'furnished':
                return formatField('furnished', 'Yes');
            case 'unfurnished':
                return formatField('furnished', 'No');
            case 'Partly Furnished':
                return formatField('semi-furnished', 'Partly');
            default:
                return '';
        }
    }
    return ''; // If no furnished value exists, return an empty string
}

function formatAgent($property)
{
    $xml = '<agent>';
    $xml .= formatField('id', $property['ufCrm13AgentId']);
    $xml .= formatField('name', $property['ufCrm13AgentName']);
    $xml .= formatField('email', $property['ufCrm13AgentEmail']);
    $xml .= formatField('phone', $property['ufCrm13AgentPhone']);
    $xml .= formatField('photo', $property['ufCrm13AgentPhoto'] ?? 'https://youtupia.com/thinkrealty/images/agent-placeholder.webp');
    $xml .= '</agent>';

    return $xml;
}

function formatPhotos($photos, $referenceNumber)
{
    global $images_to_skip;
    if (empty($photos)) {
        return '';
    }

    $xml = '<photo>';
    foreach ($photos as $photo) {
        if (!in_array($referenceNumber, $images_to_skip)) {
            $xml .= '<url last_update="' . date('Y-m-d H:i:s') . '" watermark="Yes">' . htmlspecialchars("https://connecteo.in/livrichy-property-listing/assets.php?watermark=1&imageUrl=" . $photo) . '</url>';
        } else {
            $xml .= '<url last_update="' . date('Y-m-d H:i:s') . '" watermark="Yes">' . $photo . '</url>';
        }
    }
    $xml .= '</photo>';

    return $xml;
}

function formatGeopoints($property)
{
    $geopoints = "";

    if (!empty($property['ufCrm13Latitude']) && !empty($property['ufCrm13Longitude'])) {
        $geopoints = ($property['ufCrm13Latitude'] . ',' . $property['ufCrm13Longitude'] ?? '');
    } else {
        $geopoints = ($property['ufCrm13Geopoints'] ?? '');
    }

    return formatField('geopoints', $geopoints);
}

function formatCompletionStatus($property)
{
    $status = $property['ufCrm13ProjectStatus'] ?? '';
    switch ($status) {
        case 'Completed':
        case 'ready_secondary':
            return formatField('completion_status', 'completed');
        case 'offplan':
        case 'offplan_secondary':
            return formatField('completion_status', 'off_plan');
        case 'ready_primary':
            return formatField('completion_status', 'completed_primary');
        case 'offplan_primary':
            return formatField('completion_status', 'off_plan_primary');
        default:
            return '';
    }
}

function generatePfXml($properties)
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<list last_update="' . date('Y-m-d H:i:s') . '" listing_count="' . count($properties) . '">';

    foreach ($properties as $property) {
        $xml .= '<property last_update="' . formatDate($property['updatedTime'] ?? '') . '" id="' . htmlspecialchars($property['id'] ?? '') . '">';

        $xml .= formatField('reference_number', $property['ufCrm13ReferenceNumber']);
        $xml .= formatField('permit_number', getPermitNumber($property));

        $xml .= formatField('dtcm_permit', $property['ufCrm13DtcmPermitNumber']);
        $xml .= formatField('offering_type', $property['ufCrm13OfferingType']);
        $xml .= formatField('property_type', $property['ufCrm13PropertyType']);
        $xml .= formatPriceOnApplication($property);
        $xml .= formatRentalPrice($property);

        $xml .= formatField('service_charge', $property['ufCrm13ServiceCharge']);
        $xml .= formatField('cheques', $property['ufCrm13NoOfCheques']);
        $xml .= formatField('city', $property['ufCrm13City']);
        $xml .= formatField('community', $property['ufCrm13Community']);
        $xml .= formatField('sub_community', $property['ufCrm13SubCommunity']);
        $xml .= formatField('property_name', $property['ufCrm13Tower']);

        $xml .= formatField('title_en', $property['ufCrm13TitleEn']);
        $xml .= formatField('title_ar', $property['ufCrm13TitleAr']);
        $xml .= formatField('description_en', $property['ufCrm13DescriptionEn']);
        $xml .= formatField('description_ar', $property['ufCrm13DescriptionAr']);

        $xml .= formatField('plot_size', $property['ufCrm13TotalPlotSize']);
        $xml .= formatField('size', $property['ufCrm13Size']);
        // $xml .= formatField('bedroom', $property['ufCrm13Bedroom']);
        $xml .= formatBedroom($property);
        $xml .= formatBathroom($property);

        $xml .= formatAgent($property);
        $xml .= formatField('build_year', $property['ufCrm13BuildYear']);
        $xml .= formatField('parking', $property['ufCrm13Parking']);
        $xml .= formatFurnished($property);
        $xml .= formatField('view360', $property['ufCrm_13_360_VIEW_URL']);
        $xml .= formatPhotos($property['ufCrm13PhotoLinks'], $property['ufCrm13ReferenceNumber']);
        $xml .= formatField('floor_plan', $property['ufCrm13FloorPlan']);
        $xml .= formatGeopoints($property);
        $xml .= formatField('availability_date', $property['ufCrm13AvailableFrom'], 'date');
        $xml .= formatField('video_tour_url', $property['ufCrm13VideoTourUrl']);
        $xml .= formatField('developer', $property['ufCrm13Developers']);
        $xml .= formatField('project_name', $property['ufCrm13ProjectName']);
        $xml .= formatCompletionStatus($property);

        $xml .= '</property>';
    }

    $xml .= '</list>';
    return $xml;
}

function generateBayutXml($properties)
{
    global $images_to_skip;

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<Properties last_update="' . date('Y-m-d H:i:s') . '" listing_count="' . count($properties) . '">';

    foreach ($properties as $property) {
        $xml .= '<Property id="' . htmlspecialchars($property['id'] ?? '') . '">';

        // Ensure proper CDATA wrapping and no misplaced closing tags
        $xml .= '<Property_Ref_No><![CDATA[' . ($property['ufCrm13ReferenceNumber'] ?? '') . ']]></Property_Ref_No>';
        $xml .= '<Permit_Number><![CDATA[' . getPermitNumber($property) . ']]></Permit_Number>';
        $xml .= '<Property_Status>live</Property_Status>';
        $xml .= '<Property_purpose><![CDATA[' . getPropertyPurpose($property) . ']]></Property_purpose>';
        $type = getPropertyType($property);
        $ref = strtolower($property['ufCrm13ReferenceNumber'] ?? '');

        if ($type === 'Duplex' && strpos($ref, 'villa') !== false) {
            $type = 'Duplex Villa';
        }

        $xml .= '<Property_Type><![CDATA[' . $type . ']]></Property_Type>';
        $xml .= '<Property_Size><![CDATA[' . ($property['ufCrm13Size'] ?? '') . ']]></Property_Size>';
        $xml .= '<Property_Size_Unit>SQFT</Property_Size_Unit>';

        // Ensure proper condition for optional fields
        if (!empty($property['ufCrm13TotalPlotSize'])) {
            $xml .= '<plotArea><![CDATA[' . $property['ufCrm13TotalPlotSize'] . ']]></plotArea>';
        }

        $xml .= '<Bedrooms><![CDATA[' . (($property['ufCrm13Bedroom'] === 0) ? -1 : ($property['ufCrm13Bedroom'] > 10 ? "10+" : $property['ufCrm13Bedroom'])) . ']]></Bedrooms>';
        $xml .= '<Bathrooms><![CDATA[' . ($property['ufCrm13Bathroom'] ?? '') . ']]></Bathrooms>';

        $is_offplan = ($property['ufCrm13ProjectStatus'] === 'offplan_primary' || $property['ufCrm13ProjectStatus'] === 'offplan_secondary') ? 'Yes' : 'No';
        $xml .= '<Off_plan><![CDATA[' . $is_offplan . ']]></Off_plan>';

        $xml .= '<Portals>';
        if ($property['ufCrm13BayutEnable'] === 'Y') {
            $xml .= '<Portal>Bayut</Portal>';
        }
        if ($property['ufCrm13DubizzleEnable'] === 'Y') {
            $xml .= '<Portal>Dubizzle</Portal>';
        }
        $xml .= '</Portals>';

        $xml .= '<Property_Title><![CDATA[' . ($property['ufCrm13TitleEn'] ?? '') . ']]></Property_Title>';
        $xml .= '<Property_Description><![CDATA[' . ($property['ufCrm13DescriptionEn'] ?? '') . ']]></Property_Description>';

        if (!empty($property['ufCrm13TitleAr'])) {
            $xml .= '<Property_Title_AR><![CDATA[' . ($property['ufCrm13TitleAr'] ?? '') . ']]></Property_Title_AR>';
        }
        if (!empty($property['ufCrm13DescriptionAr'])) {
            $xml .= '<Property_Description_AR><![CDATA[' . ($property['ufCrm13DescriptionAr'] ?? '') . ']]></Property_Description_AR>';
        }

        $xml .= '<Price><![CDATA[' . ($property['ufCrm13Price'] ?? '') . ']]></Price>';

        if ($property['ufCrm13RentalPeriod'] == 'Y') {
            $xml .= '<Rent_Frequency>Yearly</Rent_Frequency>';
        } elseif ($property['ufCrm13RentalPeriod'] == 'M') {
            $xml .= '<Rent_Frequency>Monthly</Rent_Frequency>';
        } elseif ($property['ufCrm13RentalPeriod'] == 'W') {
            $xml .= '<Rent_Frequency>Weekly</Rent_Frequency>';
        } elseif ($property['ufCrm13RentalPeriod'] == 'D') {
            $xml .= '<Rent_Frequency>Daily</Rent_Frequency>';
        }

        if ($property['ufCrm13Furnished'] === 'furnished') {
            $xml .= '<Furnished>Yes</Furnished>';
        } elseif ($property['ufCrm13Furnished'] === 'unfurnished') {
            $xml .= '<Furnished>No</Furnished>';
        } elseif ($property['ufCrm13Furnished'] === 'semi-furnished') {
            $xml .= '<Furnished>Partly</Furnished>';
        }

        if (!empty($property['ufCrm13SaleType'])) {
            $xml .= '<offplanDetails_saleType><![CDATA[' . ($property['ufCrm13SaleType'] ?? '') . ']]></offplanDetails_saleType>';
        }

        $xml .= '<City><![CDATA[' . ($property['ufCrm13BayutCity'] ?: $property['ufCrm13City'] ?? '') . ']]></City>';
        $xml .= '<Locality><![CDATA[' . ($property['ufCrm13BayutCommunity'] ?: $property['ufCrm13Community'] ?? '') . ']]></Locality>';
        $xml .= '<Sub_Locality><![CDATA[' . ($property['ufCrm13BayutSubCommunity'] ?: $property['ufCrm13SubCommunity'] ?? '') . ']]></Sub_Locality>';
        $xml .= '<Tower_Name><![CDATA[' . ($property['ufCrm13BayutTower'] ?: $property['ufCrm13Tower'] ?? '') . ']]></Tower_Name>';

        $xml .= '<Listing_Agent><![CDATA[' . ($property['ufCrm13AgentName'] ?? '') . ']]></Listing_Agent>';
        $xml .= '<Listing_Agent_Phone><![CDATA[' . ($property['ufCrm13AgentPhone'] ?? '') . ']]></Listing_Agent_Phone>';
        $xml .= '<Listing_Agent_Email><![CDATA[' . ($property['ufCrm13AgentEmail'] ?? '') . ']]></Listing_Agent_Email>';

        $xml .= '<Images>';
        foreach ($property['ufCrm13PhotoLinks'] ?? [] as $image) {
            if (!in_array($property['ufCrm13ReferenceNumber'], $images_to_skip)) {
                $xml .= '<Image last_update="' . date('Y-m-d H:i:s') . '"><![CDATA[https://connecteo.in/livrichy-property-listing/assets.php?watermark=1&imageUrl=' . $image . ']]></Image>';
            } else {
                $xml .= '<Image last_update="' . date('Y-m-d H:i:s') . '"><![CDATA[' . $image . ']]></Image>';
            }
        }
        $xml .= '</Images>';

        if (!empty($property['ufCrm13Amenities']) && is_array($property['ufCrm13Amenities'])) {
            $xml .= '<Features>';
            foreach ($property['ufCrm13Amenities'] as $amenity) {
                $fullName = getFullAmenityName(trim($amenity));
                $xml .= '<Feature><![CDATA[' . $fullName . ']]></Feature>';
            }
            $xml .= '</Features>';
        }

        $xml .= '</Property>';
    }

    $xml .= '</Properties>';
    return $xml;
}

function uploadFile($file, $isDocument = false)
{
    global $cloudinary;

    try {
        if (!file_exists($file)) {
            throw new Exception("File not found: " . $file);
        }

        $uploadResponse = $cloudinary->uploadApi()->upload($file, [
            'folder' => 'livrichy-uploads',
            'resource_type' => $isDocument ? 'raw' : 'image',
        ]);

        return $uploadResponse['secure_url'];
    } catch (Exception $e) {
        error_log("Error uploading image: " . $e->getMessage());
        echo "Error uploading image: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        return false;
    }
}

function fetchCurrentUser()
{
    $response = CRestCurrent::call("user.current");
    return $response['result'];
}

function isAdmin($userId)
{
    $response = CRestCurrent::call("user.admin");

    $admins = [
        1455, // Jackline Kariuki
        1043, // Kevin Singh
        154, // Eduard Shtern
        1799, // Nida Ayshathun
        1509, // VortexWeb,
        1510, // VortexWeb Services
    ];

    return in_array($userId, $admins);
}


function generateWebsiteJson($properties)
{
    $json = json_encode([
        'properties' => $properties,
        'total' => count($properties)
    ]);

    return $json;
}
