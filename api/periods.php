<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get distinct periods from monuments table
    $stmt = $db->prepare("SELECT DISTINCT period FROM monuments WHERE period IS NOT NULL AND period != '' ORDER BY period");
    $stmt->execute();
    $periods = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // If no periods found, return default historical periods
    if (empty($periods)) {
        $periods = [
            'Ancient Period',
            'Buddhist Period',
            'Gupta Period',
            'Medieval Period',
            'Mughal Period',
            'Rajput Period',
            'Maratha Period',
            'Sikh Period',
            'Colonial Period',
            'Modern Period'
        ];
    }
    
    $response = [
        'success' => true,
        'message' => 'Historical periods retrieved successfully',
        'data' => $periods
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Failed to retrieve periods: ' . $e->getMessage(),
        'data' => []
    ];
}

echo json_encode($response);
?>