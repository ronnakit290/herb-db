<?php
require_once 'connect.php';

// Handle form submissions
$message = '';
$messageType = '';
$selectedVillage = $_GET['village'] ?? '';
$editHerbId = $_GET['edit'] ?? '';
$editHerb = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
            case 'update':
                $name = $_POST['name'] ?? '';
                $englishName = $_POST['englishName'] ?? '';
                $scientificName = $_POST['scientificName'] ?? '';
                $familyId = $_POST['familyId'] ?? null;
                $villageId = $_POST['villageId'] ?? null;
                $description = $_POST['description'] ?? '';
                $herbId = $_POST['herbId'] ?? null;
                
                if (empty($name)) {
                    $message = 'กรุณากรอกชื่อสมุนไพร';
                    $messageType = 'error';
                } else {
                    try {
                        if ($_POST['action'] === 'update' && $herbId) {
                            $sql = "UPDATE herbs SET name = :name, englishName = :englishName, scientificName = :scientificName, familyId = :familyId, villageId = :villageId, description = :description WHERE id = :id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $herbId);
                        } else {
                            $sql = "INSERT INTO herbs (name, englishName, scientificName, familyId, villageId, description) VALUES (:name, :englishName, :scientificName, :familyId, :villageId, :description)";
                            $stmt = $pdo->prepare($sql);
                        }
                        
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':englishName', $englishName);
                        $stmt->bindParam(':scientificName', $scientificName);
                        $stmt->bindParam(':familyId', $familyId);
                        $stmt->bindParam(':villageId', $villageId);
                        $stmt->bindParam(':description', $description);
                        
                        if ($stmt->execute()) {
                            $message = $_POST['action'] === 'update' ? 'อัปเดตสมุนไพรสำเร็จ!' : 'เพิ่มสมุนไพรสำเร็จ!';
                            $messageType = 'success';
                            // Redirect to clear form
                            $redirectUrl = 'herb.php';
                            if ($selectedVillage) {
                                $redirectUrl .= '?village=' . $selectedVillage;
                            }
                            header("Location: $redirectUrl");
                            exit;
                        } else {
                            $message = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                            $messageType = 'error';
                        }
                    } catch (Exception $e) {
                        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                        $messageType = 'error';
                    }
                }
                break;
                
            case 'delete':
                $herbId = $_POST['herbId'] ?? null;
                if ($herbId) {
                    try {
                        $sql = "DELETE FROM herbs WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $herbId);
                        
                        if ($stmt->execute()) {
                            $message = 'ลบสมุนไพรสำเร็จ!';
                            $messageType = 'success';
                        } else {
                            $message = 'เกิดข้อผิดพลาดในการลบข้อมูล';
                            $messageType = 'error';
                        }
                    } catch (Exception $e) {
                        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                        $messageType = 'error';
                    }
                }
                break;
        }
    }
}

// Get herb for editing
if ($editHerbId) {
    try {
        $sql = "SELECT h.*, f.name as family_name, v.name as village_name 
                FROM herbs h 
                LEFT JOIN families f ON h.familyId = f.id 
                LEFT JOIN villages v ON h.villageId = v.id 
                WHERE h.id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $editHerbId);
        $stmt->execute();
        $editHerb = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get herbs list
$herbs = [];
try {
    $sql = "SELECT h.*, f.name as family_name, v.name as village_name 
            FROM herbs h 
            LEFT JOIN families f ON h.familyId = f.id 
            LEFT JOIN villages v ON h.villageId = v.id";
    
    $params = [];
    if ($selectedVillage) {
        $sql .= " WHERE h.villageId = :village";
        $params[':village'] = $selectedVillage;
    }
    
    $sql .= " ORDER BY h.id DESC";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $herbs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage();
    $messageType = 'error';
}

// Get families for dropdown
$families = [];
try {
    $sql = "SELECT id, name FROM families ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $families = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Ignore error
}

// Get villages for dropdown and cards
$villages = [];
try {
    $sql = "SELECT id, name FROM villages ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $villages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Ignore error
}

// Get selected village name
$selectedVillageName = '';
if ($selectedVillage) {
    foreach ($villages as $village) {
        if ($village['id'] == $selectedVillage) {
            $selectedVillageName = $village['name'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสมุนไพร</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mb-4">
                <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                    🏠 กลับหน้าหลัก
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">ระบบจัดการสมุนไพร</h1>
        </div>
        
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$selectedVillage): ?>
            <!-- Village Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">เลือกหมู่บ้าน</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($villages as $village): ?>
                        <a href="?village=<?php echo $village['id']; ?>" 
                           class="block bg-white border-2 border-gray-200 rounded-lg p-6 text-center hover:border-blue-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($village['name']); ?></h3>
                            <p class="text-gray-600">คลิกเพื่อดูสมุนไพรในหมู่บ้านนี้</p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Filter Status -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex justify-between items-center">
                <span>กำลังแสดงสมุนไพรจากหมู่บ้าน: <strong><?php echo htmlspecialchars($selectedVillageName); ?></strong></span>
                <a href="herb.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">กลับไปเลือกหมู่บ้าน</a>
            </div>
            
            <!-- Herb Form - Show only when action=create, action=update, or edit parameter exists -->
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'update') || isset($_GET['edit'])): ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <?php echo $editHerb ? 'แก้ไขข้อมูลสมุนไพร' : 'เพิ่มสมุนไพรใหม่'; ?>
                </h2>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="<?php echo $editHerb ? 'update' : 'create'; ?>">
                    <input type="hidden" name="villageId" value="<?php echo $selectedVillage; ?>">
                    <?php if ($editHerb): ?>
                        <input type="hidden" name="herbId" value="<?php echo $editHerb['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">ชื่อสมุนไพร *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo $editHerb ? htmlspecialchars($editHerb['name']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="englishName" class="block text-sm font-medium text-gray-700 mb-2">ชื่อภาษาอังกฤษ</label>
                            <input type="text" id="englishName" name="englishName" 
                                   value="<?php echo $editHerb ? htmlspecialchars($editHerb['englishName']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="scientificName" class="block text-sm font-medium text-gray-700 mb-2">ชื่อวิทยาศาสตร์</label>
                            <input type="text" id="scientificName" name="scientificName" 
                                   value="<?php echo $editHerb ? htmlspecialchars($editHerb['scientificName']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="familyId" class="block text-sm font-medium text-gray-700 mb-2">วงศ์พืช</label>
                            <select id="familyId" name="familyId" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- เลือกวงศ์พืช --</option>
                                <?php foreach ($families as $family): ?>
                                    <option value="<?php echo $family['id']; ?>" 
                                            <?php echo ($editHerb && $editHerb['familyId'] == $family['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($family['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">คำอธิบาย</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $editHerb ? htmlspecialchars($editHerb['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="px-8 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors shadow-md font-medium">
                            <?php echo $editHerb ? '✅ อัปเดต' : '💾 บันทึก'; ?>
                        </button>
                        <?php if ($editHerb): ?>
                            <a href="?village=<?php echo $selectedVillage; ?>" class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors inline-block shadow-md font-medium">
                                ❌ ยกเลิก
                            </a>
                        <?php else: ?>
                            <button type="reset" class="px-8 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors shadow-md font-medium">
                                🗑️ ล้างข้อมูล
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Add Herb Button - Show only when form is not displayed -->
            <?php if (!isset($_GET['action']) && !isset($_GET['edit'])): ?>
            <div class="text-center mb-8">
                <a href="?village=<?php echo $selectedVillage; ?>&action=create" class="inline-block px-8 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors shadow-md font-medium">
                    ➕ เพิ่มสมุนไพรใหม่
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Herbs List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">รายการสมุนไพร</h2>
                
                <?php if (empty($herbs)): ?>
                    <p class="text-gray-600 text-center py-8">ไม่มีข้อมูลสมุนไพรในหมู่บ้านนี้</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($herbs as $herb): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($herb['name']); ?></h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                            <div>
                                                <p><strong>ชื่อภาษาอังกฤษ:</strong> <?php echo htmlspecialchars($herb['englishName'] ?: 'ไม่ระบุ'); ?></p>
                                                <p><strong>ชื่อวิทยาศาสตร์:</strong> <?php echo htmlspecialchars($herb['scientificName'] ?: 'ไม่ระบุ'); ?></p>
                                            </div>
                                            <div>
                                                <p><strong>วงศ์:</strong> <?php echo htmlspecialchars($herb['family_name'] ?: 'ไม่ระบุ'); ?></p>
                                                <p><strong>หมู่บ้าน:</strong> <?php echo htmlspecialchars($herb['village_name'] ?: 'ไม่ระบุ'); ?></p>
                                            </div>
                                        </div>
                                        <?php if ($herb['description']): ?>
                                            <p class="mt-2 text-gray-700"><strong>คำอธิบาย:</strong> <?php echo htmlspecialchars($herb['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex space-x-2 ml-4">
                                        <a href="?village=<?php echo $selectedVillage; ?>&edit=<?php echo $herb['id']; ?>" 
                                           class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors shadow-md font-medium">
                                            ✏️ แก้ไข
                                        </a>
                                        
                                        <form method="POST" class="inline" onsubmit="return confirm('คุณต้องการลบสมุนไพร &quot;<?php echo htmlspecialchars($herb['name']); ?>&quot; หรือไม่?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="herbId" value="<?php echo $herb['id']; ?>">
                                            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors shadow-md font-medium">
                                                🗑️ ลบ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>