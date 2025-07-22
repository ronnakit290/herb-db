<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'connect.php';

// Get action from query parameter
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            createVillage();
            break;
        case 'read':
            readVillages();
            break;
        case 'update':
            updateVillage();
            break;
        case 'delete':
            deleteVillage();
            break;
        case 'get':
            getVillage();
            break;
        case 'sub_districts':
            getSubDistricts();
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action. Available actions: create, read, update, delete, get, sub_districts'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// Create new village
function createVillage() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || empty(trim($input['name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Village name is required'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO villages (name, subDistrictId) VALUES (?, ?)");
        $result = $stmt->execute([
            trim($input['name']),
            $input['subDistrictId'] ?? null
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Village created successfully',
                'id' => $pdo->lastInsertId()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create village'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Read all villages
function readVillages() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT v.id, v.name, v.subDistrictId, 
                   sd.name as sub_district_name
            FROM villages v
            LEFT JOIN sub_districts sd ON v.subDistrictId = sd.id
            ORDER BY v.name
        ");
        $villages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $villages
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Update village
function updateVillage() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['name']) || empty(trim($input['name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Village ID and name are required'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE villages SET name = ?, subDistrictId = ? WHERE id = ?");
        $result = $stmt->execute([
            trim($input['name']),
            $input['subDistrictId'] ?? null,
            $input['id']
        ]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Village updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Village not found or no changes made'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Delete village
function deleteVillage() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Village ID is required'
        ]);
        return;
    }
    
    try {
        // Check if village is being used by any herbs
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM herbs WHERE villageId = ?");
        $checkStmt->execute([$input['id']]);
        $herbCount = $checkStmt->fetchColumn();
        
        if ($herbCount > 0) {
            echo json_encode([
                'success' => false,
                'message' => "Cannot delete village. It is being used by {$herbCount} herb(s)."
            ]);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM villages WHERE id = ?");
        $result = $stmt->execute([$input['id']]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Village deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Village not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Get single village
function getVillage() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'Village ID is required'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT v.id, v.name, v.subDistrictId, 
                   sd.name as sub_district_name
            FROM villages v
            LEFT JOIN sub_districts sd ON v.subDistrictId = sd.id
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        $village = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($village) {
            echo json_encode([
                'success' => true,
                'data' => $village
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Village not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Get all sub-districts
function getSubDistricts() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT id, name FROM sub_districts ORDER BY name");
        $subDistricts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $subDistricts
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}
?>