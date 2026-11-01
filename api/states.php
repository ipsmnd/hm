<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get distinct states from monuments table
    $stmt = $db->prepare("SELECT DISTINCT state FROM monuments WHERE state IS NOT NULL AND state != '' ORDER BY state");
    $stmt->execute();
    $states = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // If no states found, return default Indian states
    if (empty($states)) {
        $states = [
            'Andhra Pradesh',
            'Arunachal Pradesh', 
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Delhi',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Manipur',
            'Meghalaya',
            'Mizoram',
            'Nagaland',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Sikkim',
            'Tamil Nadu',
            'Telangana',
            'Tripura',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal'
        ];
    }
    
    $response = [
        'success' => true,
        'message' => 'States retrieved successfully',
        'data' => $states
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Failed to retrieve states: ' . $e->getMessage(),
        'data' => []
    ];
}

echo json_encode($response);
?>