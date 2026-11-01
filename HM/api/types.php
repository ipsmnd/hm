<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get distinct types from monuments table
    $stmt = $db->prepare("SELECT DISTINCT type FROM monuments WHERE type IS NOT NULL AND type != '' ORDER BY type");
    $stmt->execute();
    $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // If no types found, return default monument types
    if (empty($types)) {
        $types = [
            'Temple',
            'Fort',
            'Palace',
            'Tomb',
            'Mosque',
            'Church',
            'Monastery',
            'Cave',
            'Stepwell',
            'Gateway',
            'Minaret',
            'Memorial',
            'Observatory',
            'Garden',
            'Bridge'
        ];
    }
    
    $response = [
        'success' => true,
        'message' => 'Monument types retrieved successfully',
        'data' => $types
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Failed to retrieve types: ' . $e->getMessage(),
        'data' => []
    ];
}

echo json_encode($response);
?>