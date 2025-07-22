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
            createFamily();
            break;
        case 'read':
            readFamilies();
            break;
        case 'update':
            updateFamily();
            break;
        case 'delete':
            deleteFamily();
            break;
        case 'get':
            getFamily();
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action. Available actions: create, read, update, delete, get'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// Create new family
function createFamily() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || empty(trim($input['name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Family name is required'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO families (name, description) VALUES (?, ?)");
        $result = $stmt->execute([
            trim($input['name']),
            $input['description'] ?? null
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Family created successfully',
                'id' => $pdo->lastInsertId()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create family'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Read all families
function readFamilies() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT id, name, description FROM families ORDER BY name");
        $families = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $families
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Update family
function updateFamily() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['name']) || empty(trim($input['name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Family ID and name are required'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE families SET name = ?, description = ? WHERE id = ?");
        $result = $stmt->execute([
            trim($input['name']),
            $input['description'] ?? null,
            $input['id']
        ]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Family updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Family not found or no changes made'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Delete family
function deleteFamily() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Family ID is required'
        ]);
        return;
    }
    
    try {
        // Check if family is being used by any herbs
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM herbs WHERE familyId = ?");
        $checkStmt->execute([$input['id']]);
        $herbCount = $checkStmt->fetchColumn();
        
        if ($herbCount > 0) {
            echo json_encode([
                'success' => false,
                'message' => "Cannot delete family. It is being used by {$herbCount} herb(s)."
            ]);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM families WHERE id = ?");
        $result = $stmt->execute([$input['id']]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Family deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Family not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Get single family
function getFamily() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'Family ID is required'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, description FROM families WHERE id = ?");
        $stmt->execute([$id]);
        $family = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($family) {
            echo json_encode([
                'success' => true,
                'data' => $family
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Family not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}
?>