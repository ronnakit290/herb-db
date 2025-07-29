<?php
require_once 'connect.php';

// Handle form submissions
$message = '';
$messageType = '';
$editFamilyId = $_GET['edit'] ?? '';
$editFamily = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
            case 'update':
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $familyId = $_POST['familyId'] ?? null;
                
                if (empty($name)) {
                    $message = 'กรุณากรอกชื่อตระกูลพืช';
                    $messageType = 'error';
                } else {
                    try {
                        if ($_POST['action'] === 'update' && $familyId) {
                            $sql = "UPDATE families SET name = :name, description = :description WHERE id = :id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $familyId);
                        } else {
                            $sql = "INSERT INTO families (name, description) VALUES (:name, :description)";
                            $stmt = $pdo->prepare($sql);
                        }
                        
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':description', $description);
                        
                        if ($stmt->execute()) {
                            $message = $_POST['action'] === 'update' ? 'อัปเดตตระกูลพืชสำเร็จ!' : 'เพิ่มตระกูลพืชสำเร็จ!';
                            $messageType = 'success';
                            // Redirect to clear form
                            header("Location: family-management.php");
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
                $familyId = $_POST['familyId'] ?? null;
                if ($familyId) {
                    try {
                        $sql = "DELETE FROM families WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $familyId);
                        
                        if ($stmt->execute()) {
                            $message = 'ลบตระกูลพืชสำเร็จ!';
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

// Get family for editing
if ($editFamilyId) {
    try {
        $sql = "SELECT * FROM families WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $editFamilyId);
        $stmt->execute();
        $editFamily = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get families list
$families = [];
try {
    $sql = "SELECT * FROM families ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $families = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage();
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลตระกูลพืช</title>
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
            <h1 class="text-3xl font-bold text-gray-800 mb-2">🌱 จัดการข้อมูลตระกูลพืช</h1>
        </div>
        
        <!-- Navigation -->
        <div class="text-center mb-8">
            <a href="herb.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md mx-2 transition-colors">จัดการสมุนไพร</a>
            <a href="family-management.php" class="inline-block bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md mx-2 transition-colors">จัดการตระกูลพืช</a>
            <a href="village-management.php" class="inline-block bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-md mx-2 transition-colors">จัดการหมู่บ้าน</a>
        </div>
        
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Family Form - Show only when action=create, action=update, or edit parameter exists -->
        <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'update') || isset($_GET['edit'])): ?>
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                <?php echo $editFamily ? 'แก้ไขตระกูลพืช' : 'เพิ่มตระกูลพืชใหม่'; ?>
            </h2>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $editFamily ? 'update' : 'create'; ?>">
                <?php if ($editFamily): ?>
                    <input type="hidden" name="familyId" value="<?php echo $editFamily['id']; ?>">
                <?php endif; ?>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">ชื่อตระกูลพืช *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo $editFamily ? htmlspecialchars($editFamily['name']) : ''; ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">คำอธิบาย</label>
                    <textarea id="description" name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $editFamily ? htmlspecialchars($editFamily['description']) : ''; ?></textarea>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" class="px-8 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors shadow-md font-medium">
                        <?php echo $editFamily ? '✅ อัปเดต' : '💾 บันทึก'; ?>
                    </button>
                    <?php if ($editFamily): ?>
                        <a href="family-management.php" class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors inline-block shadow-md font-medium">
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
        
        <!-- Add Family Button - Show only when form is not displayed -->
        <?php if (!isset($_GET['action']) && !isset($_GET['edit'])): ?>
        <div class="text-center mb-8">
            <a href="?action=create" class="inline-block px-8 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors shadow-md font-medium">
                ➕ เพิ่มตระกูลพืชใหม่
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Families List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">รายการตระกูลพืช</h2>
            
            <?php if (empty($families)): ?>
                <p class="text-gray-600 text-center py-8">ไม่มีข้อมูลตระกูลพืช</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($families as $family): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($family['name']); ?></h3>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($family['description'] ?: 'ไม่มีคำอธิบาย'); ?></p>
                                </div>
                                
                                <div class="flex space-x-2 ml-4">
                                    <a href="?edit=<?php echo $family['id']; ?>" 
                                       class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors shadow-md font-medium">
                                        ✏️ แก้ไข
                                    </a>
                                    <form method="POST" class="inline" onsubmit="return confirm('คุณต้องการลบตระกูลพืช &quot;<?php echo htmlspecialchars($family['name']); ?>&quot; หรือไม่?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="familyId" value="<?php echo $family['id']; ?>">
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
    </div>
</body>
</html>