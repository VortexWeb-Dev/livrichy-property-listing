<?php
require_once(__DIR__ . '/crest/crest.php');
require_once(__DIR__ . '/crest/settings.php');

// Function to fetch property details by ID from the SPA
function fetchPropertyDetails($id)
{
    $response = CRest::call('crm.item.get', [
        'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
        'id' => $id
    ]);

    return $response['result']['item'] ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['property_ids']) && !empty($_POST['property_ids'])) {
        $propertyIds = $_POST['property_ids'];
        // $portal = $_POST['portal'];

        // Start output buffering
        ob_start();

        // Fetch property details
        $properties = [];
        foreach ($propertyIds as $id) {
            try {
                $property = fetchPropertyDetails($id);
                if ($property) {
                    $properties[] = $property;
                }
            } catch (Exception $e) {
                echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
        }

        // Generate XML
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><list/>');
        $xml->addAttribute('last_update', date('y-m-d H:i:s')); // Current date-time
        $xml->addAttribute('listing_count', count($properties));

        foreach ($properties as $property) {
            $propertyNode = $xml->addChild('property');
            $propertyNode->addAttribute('last_update', date('y-m-d H:i:s', strtotime($property['updatedTime'] ?? '')));
            $propertyNode->addAttribute('id', $property['id'] ?? '');

            addCDataElement($propertyNode, 'reference_number', $property['ufCrm13ReferenceNumber'] ?? '');
            addCDataElement($propertyNode, 'permit_number', $property['ufCrm13PermitNumber'] ?? '');

            if (isset($property['ufCrm13RentalPeriod']) && $property['ufCrm13RentalPeriod'] === 'M') {
                addCDataElement($propertyNode->addChild('price'), 'monthly', $property['ufCrm13Price'] ?? '');
            }

            addCDataElement($propertyNode, 'offering_type', $property['ufCrm13OfferingType'] ?? '');
            addCDataElement($propertyNode, 'property_type', $property['ufCrm13PropertyType'] ?? '');

            addCDataElement($propertyNode, 'geopoints', $property['ufCrm13Geopoints'] ?? '');
            addCDataElement($propertyNode, 'city', $property['ufCrm13City'] ?? '');
            addCDataElement($propertyNode, 'community', $property['ufCrm13Community'] ?? '');
            addCDataElement($propertyNode, 'sub_community', $property['ufCrm13SubCommunity'] ?? '');
            addCDataElement($propertyNode, 'title_en', $property['ufCrm13TitleEn'] ?? '');
            addCDataElement($propertyNode, 'description_en', $property['ufCrm13DescriptionEn'] ?? '');
            addCDataElement($propertyNode, 'size', $property['ufCrm13Size'] ?? '');
            addCDataElement($propertyNode, 'bedroom', $property['ufCrm13Bedroom'] ?? '');
            addCDataElement($propertyNode, 'bathroom', $property['ufCrm13Bathroom'] ?? '');

            $agentNode = $propertyNode->addChild('agent');
            addCDataElement($agentNode, 'id', $property['ufCrm13AgentId'] ?? '');
            addCDataElement($agentNode, 'name', $property['ufCrm13AgentName'] ?? '');
            addCDataElement($agentNode, 'email', $property['ufCrm13AgentEmail'] ?? '');
            addCDataElement($agentNode, 'phone', $property['ufCrm13AgentPhone'] ?? '');
            addCDataElement($agentNode, 'photo', $property['ufCrm13AgentPhoto'] ?? '');

            $photoNode = $propertyNode->addChild('photo');
            foreach ($property['ufCrm13Photos'] as $photo) {

                $urlNode = addCDataElement($photoNode, 'url', $photo);
                $urlNode->addAttribute('last_update', date('Y-m-d H:i:s'));
                $urlNode->addAttribute('watermark', 'Yes');
            }

            addCDataElement($propertyNode, 'parking', $property['ufCrm13Parking'] ?? '');
            addCDataElement($propertyNode, 'furnished', $property['ufCrm13Furnished'] ?? '');
            addCDataElement($propertyNode, 'price_on_application', $property['ufCrm13PriceOnApplication'] ?? '');
        }

        // End output buffering and get content
        $content = ob_get_clean();
        $fileName = 'test' . '_properties_' . date('y-m-d_H-i-s') . '.xml';

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $xml->asXML();
        exit;
    } else {
        echo "No properties selected or portal not specified.";
    }
} else {
    echo "Invalid request method.";
}

// Helper function to add CDATA
function addCDataElement(SimpleXMLElement $node, $name, $value)
{
    $child = $node->addChild($name);
    $dom = dom_import_simplexml($child);
    $dom->appendChild($dom->ownerDocument->createCDATASection($value));

    return $child;
}
