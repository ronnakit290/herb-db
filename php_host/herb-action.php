<?php
include "connect.php";

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createHerb();
            break;
        case 'update':
            updateHerb();
            break;
        case 'delete':
            deleteHerb();
            break;
        case 'read':
        case 'list':
            readHerbs();
            break;
        case 'get':
            getHerb();
            break;
        case 'families':
            getFamilies();
            break;
        case 'villages':
            getVillages();
            break;
        default:
            echo json_encode(['error' => 'Invalid action. Use: create, update, delete, read, get, families, villages']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

// Create new herb
function createHerb() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $name = $input['name'] ?? '';
    $englishName = $input['englishName'] ?? null;
    $description = $input['description'] ?? null;
    $scientificName = $input['scientificName'] ?? null;
    $familyId = $input['familyId'] ?? null;
    $villageId = $input['villageId'] ?? null;
    
    if (empty($name)) {
        echo json_encode(['error' => 'Name is required']);
        return;
    }
    
    $sql = "INSERT INTO herbs (name, englishName, description, scientificName, familyId, villageId) 
            VALUES (:name, :englishName, :description, :scientificName, :familyId, :villageId)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':englishName', $englishName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':scientificName', $scientificName);
    $stmt->bindParam(':familyId', $familyId);
    $stmt->bindParam(':villageId', $villageId);
    
    if ($stmt->execute()) {
        $herbId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $herbId, 'message' => 'Herb created successfully']);
    } else {
        echo json_encode(['error' => 'Failed to create herb']);
    }
}

// Update existing herb
function updateHerb() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $id = $input['id'] ?? $_GET['id'] ?? '';
    $name = $input['name'] ?? '';
    $englishName = $input['englishName'] ?? null;
    $description = $input['description'] ?? null;
    $scientificName = $input['scientificName'] ?? null;
    $familyId = $input['familyId'] ?? null;
    $villageId = $input['villageId'] ?? null;
    
    if (empty($id) || empty($name)) {
        echo json_encode(['error' => 'ID and Name are required']);
        return;
    }
    
    $sql = "UPDATE herbs SET name = :name, englishName = :englishName, description = :description, 
            scientificName = :scientificName, familyId = :familyId, villageId = :villageId 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':englishName', $englishName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':scientificName', $scientificName);
    $stmt->bindParam(':familyId', $familyId);
    $stmt->bindParam(':villageId', $villageId);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Herb updated successfully']);
        } else {
            echo json_encode(['error' => 'Herb not found or no changes made']);
        }
    } else {
        echo json_encode(['error' => 'Failed to update herb']);
    }
}

// Delete herb
function deleteHerb() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $id = $input['id'] ?? $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $sql = "DELETE FROM herbs WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Herb deleted successfully']);
        } else {
            echo json_encode(['error' => 'Herb not found']);
        }
    } else {
        echo json_encode(['error' => 'Failed to delete herb']);
    }
}

// Read all herbs
function readHerbs() {
    global $pdo;
    
    $limit = $_GET['limit'] ?? 100;
    $offset = $_GET['offset'] ?? 0;
    $village = $_GET['village'] ?? null;
    
    $sql = "SELECT h.*, f.name as family_name, v.name as village_name 
            FROM herbs h 
            LEFT JOIN families f ON h.familyId = f.id 
            LEFT JOIN villages v ON h.villageId = v.id";
    
    $params = [];
    
    if ($village) {
        $sql .= " WHERE h.villageId = :village";
        $params[':village'] = $village;
    }
    
    $sql .= " ORDER BY h.id DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    if ($village) {
        $stmt->bindParam(':village', $village, PDO::PARAM_INT);
    }
    
    if ($stmt->execute()) {
        $herbs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $herbs]);
    } else {
        echo json_encode(['error' => 'Failed to fetch herbs']);
    }
}

// Get single herb by ID
function getHerb() {
    global $pdo;
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $sql = "SELECT h.*, f.name as family_name, v.name as village_name 
            FROM herbs h 
            LEFT JOIN families f ON h.familyId = f.id 
            LEFT JOIN villages v ON h.villageId = v.id 
            WHERE h.id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $herb = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($herb) {
            echo json_encode(['success' => true, 'data' => $herb]);
        } else {
            echo json_encode(['error' => 'Herb not found']);
        }
    } else {
        echo json_encode(['error' => 'Failed to fetch herb']);
    }
}

// Get all families for dropdown
function getFamilies() {
    global $pdo;
    
    $sql = "SELECT id, name FROM families ORDER BY name";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute()) {
        $families = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $families]);
    } else {
        echo json_encode(['error' => 'Failed to fetch families']);
    }
}

// Get all villages for dropdown
function getVillages() {
    global $pdo;
    
    $sql = "SELECT id, name FROM villages ORDER BY name";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute()) {
        $villages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $villages]);
    } else {
        echo json_encode(['error' => 'Failed to fetch villages']);
    }
}

?>
