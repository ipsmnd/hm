<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';

class MonumentsAPI {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $this->getMonument($_GET['id']);
                } else {
                    $this->getMonuments();
                }
                break;
            case 'POST':
                $this->createMonument();
                break;
            case 'PUT':
                $this->updateMonument();
                break;
            case 'DELETE':
                $this->deleteMonument();
                break;
            default:
                $this->sendResponse(405, false, 'Method not allowed');
        }
    }
    
    private function getMonuments() {
        try {
            $query = "SELECT * FROM monuments WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $query .= " AND (name LIKE ? OR description LIKE ? OR city LIKE ? OR state LIKE ?)";
                $searchTerm = '%' . $_GET['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (isset($_GET['state']) && !empty($_GET['state'])) {
                $query .= " AND state = ?";
                $params[] = $_GET['state'];
            }
            
            if (isset($_GET['period']) && !empty($_GET['period'])) {
                $query .= " AND period = ?";
                $params[] = $_GET['period'];
            }
            
            if (isset($_GET['type']) && !empty($_GET['type'])) {
                $query .= " AND type = ?";
                $params[] = $_GET['type'];
            }
            
            if (isset($_GET['unesco_site']) && $_GET['unesco_site'] !== '') {
                $query .= " AND unesco_site = ?";
                $params[] = (int)$_GET['unesco_site'];
            }
            
            if (isset($_GET['featured']) && $_GET['featured'] == 'true') {
                $query .= " AND featured = 1";
            }
            
            if (isset($_GET['exclude'])) {
                $query .= " AND id != ?";
                $params[] = $_GET['exclude'];
            }
            
            // Sorting
            $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';
            $allowedSorts = ['name', 'state', 'period', 'created_at', 'popularity'];
            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'name';
            }
            $query .= " ORDER BY $sortBy ASC";
            
            // Pagination
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
            $offset = ($page - 1) * $limit;
            
            // Get total count
            $countQuery = str_replace('SELECT *', 'SELECT COUNT(*)', explode(' ORDER BY', $query)[0]);
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            // Get paginated results
            $query .= " LIMIT $limit OFFSET $offset";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $monuments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert boolean fields
            foreach ($monuments as &$monument) {
                $monument['unesco_site'] = (bool)$monument['unesco_site'];
                $monument['featured'] = (bool)$monument['featured'];
            }
            
            $this->sendResponse(200, true, 'Monuments retrieved successfully', $monuments, $total);
            
        } catch (Exception $e) {
            $this->sendResponse(500, false, 'Failed to retrieve monuments: ' . $e->getMessage());
        }
    }
    
    private function getMonument($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM monuments WHERE id = ?");
            $stmt->execute([$id]);
            $monument = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($monument) {
                $monument['unesco_site'] = (bool)$monument['unesco_site'];
                $monument['featured'] = (bool)$monument['featured'];
                $this->sendResponse(200, true, 'Monument retrieved successfully', $monument);
            } else {
                $this->sendResponse(404, false, 'Monument not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, false, 'Failed to retrieve monument: ' . $e->getMessage());
        }
    }
    
    private function createMonument() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['name']) || !isset($input['description'])) {
                $this->sendResponse(400, false, 'Missing required fields');
                return;
            }
            
            $query = "INSERT INTO monuments (name, city, state, description, historical_significance, 
                     architecture, period, type, builder, built_year, unesco_site, featured, 
                     opening_hours, entry_fee, best_time, image_url, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $input['name'],
                $input['city'] ?? '',
                $input['state'] ?? '',
                $input['description'],
                $input['historical_significance'] ?? '',
                $input['architecture'] ?? '',
                $input['period'] ?? '',
                $input['type'] ?? '',
                $input['builder'] ?? '',
                $input['built_year'] ?? '',
                isset($input['unesco_site']) ? (int)$input['unesco_site'] : 0,
                isset($input['featured']) ? (int)$input['featured'] : 0,
                $input['opening_hours'] ?? '',
                $input['entry_fee'] ?? '',
                $input['best_time'] ?? '',
                $input['image_url'] ?? ''
            ]);
            
            if ($result) {
                $monumentId = $this->db->lastInsertId();
                $this->sendResponse(201, true, 'Monument created successfully', ['id' => $monumentId]);
            } else {
                $this->sendResponse(500, false, 'Failed to create monument');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, false, 'Failed to create monument: ' . $e->getMessage());
        }
    }
    
    private function updateMonument() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                $this->sendResponse(400, false, 'Missing monument ID');
                return;
            }
            
            // Check if monument exists
            $stmt = $this->db->prepare("SELECT id FROM monuments WHERE id = ?");
            $stmt->execute([$input['id']]);
            if (!$stmt->fetch()) {
                $this->sendResponse(404, false, 'Monument not found');
                return;
            }
            
            $query = "UPDATE monuments SET name = ?, city = ?, state = ?, description = ?, 
                     historical_significance = ?, architecture = ?, period = ?, type = ?, 
                     builder = ?, built_year = ?, unesco_site = ?, featured = ?, 
                     opening_hours = ?, entry_fee = ?, best_time = ?, image_url = ?, 
                     updated_at = NOW() WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $input['name'] ?? '',
                $input['city'] ?? '',
                $input['state'] ?? '',
                $input['description'] ?? '',
                $input['historical_significance'] ?? '',
                $input['architecture'] ?? '',
                $input['period'] ?? '',
                $input['type'] ?? '',
                $input['builder'] ?? '',
                $input['built_year'] ?? '',
                isset($input['unesco_site']) ? (int)$input['unesco_site'] : 0,
                isset($input['featured']) ? (int)$input['featured'] : 0,
                $input['opening_hours'] ?? '',
                $input['entry_fee'] ?? '',
                $input['best_time'] ?? '',
                $input['image_url'] ?? '',
                $input['id']
            ]);
            
            if ($result) {
                $this->sendResponse(200, true, 'Monument updated successfully');
            } else {
                $this->sendResponse(500, false, 'Failed to update monument');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, false, 'Failed to update monument: ' . $e->getMessage());
        }
    }
    
    private function deleteMonument() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                $this->sendResponse(400, false, 'Missing monument ID');
                return;
            }
            
            $stmt = $this->db->prepare("DELETE FROM monuments WHERE id = ?");
            $result = $stmt->execute([$input['id']]);
            
            if ($stmt->rowCount() > 0) {
                $this->sendResponse(200, true, 'Monument deleted successfully');
            } else {
                $this->sendResponse(404, false, 'Monument not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, false, 'Failed to delete monument: ' . $e->getMessage());
        }
    }
    
    private function sendResponse($code, $success, $message, $data = null, $total = null) {
        http_response_code($code);
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($total !== null) {
            $response['total'] = $total;
        }
        
        echo json_encode($response);
        exit;
    }
}

// Initialize and handle request
$api = new MonumentsAPI();
$api->handleRequest();
?>