<?php
require_once 'connect.php';

// Handle form submissions
$message = '';
$messageType = '';
$editVillage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = trim($_POST['name']);
                $subDistrictId = !empty($_POST['subDistrictId']) ? $_POST['subDistrictId'] : null;

                if (!empty($name)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO villages (name, subDistrictId) VALUES (?, ?)");
                        $stmt->execute([$name, $subDistrictId]);
                        $message = 'เพิ่มหมู่บ้านสำเร็จ!';
                        $messageType = 'success';
                    } catch (PDOException $e) {
                        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                        $messageType = 'error';
                    }
                } else {
                    $message = 'กรุณากรอกชื่อหมู่บ้าน';
                    $messageType = 'error';
                }
                break;

            case 'update':
                $id = $_POST['id'];
                $name = trim($_POST['name']);
                $subDistrictId = !empty($_POST['subDistrictId']) ? $_POST['subDistrictId'] : null;

                if (!empty($name) && !empty($id)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE villages SET name = ?, subDistrictId = ? WHERE id = ?");
                        $stmt->execute([$name, $subDistrictId, $id]);
                        $message = 'อัปเดตหมู่บ้านสำเร็จ!';
                        $messageType = 'success';
                    } catch (PDOException $e) {
                        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                        $messageType = 'error';
                    }
                } else {
                    $message = 'ข้อมูลไม่ครบถ้วน';
                    $messageType = 'error';
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                if (!empty($id)) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM villages WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'ลบหมู่บ้านสำเร็จ!';
                        $messageType = 'success';
                    } catch (PDOException $e) {
                        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                        $messageType = 'error';
                    }
                }
                break;
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM villages WHERE id = ?");
        $stmt->execute([$editId]);
        $editVillage = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = 'ไม่สามารถโหลดข้อมูลสำหรับแก้ไขได้';
        $messageType = 'error';
    }
}

// Load villages
try {
    $stmt = $pdo->query("
        SELECT v.*, sd.name as sub_district_name 
        FROM villages v 
        LEFT JOIN sub_districts sd ON v.subDistrictId = sd.id 
        ORDER BY v.name
    ");
    $villages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $villages = [];
    $message = 'ไม่สามารถโหลดข้อมูลหมู่บ้านได้';
    $messageType = 'error';
}

// Load sub-districts for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM sub_districts ORDER BY name");
    $subDistricts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $subDistricts = [];
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมู่บ้าน - ระบบจัดการสมุนไพร</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Thai', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-4">
                <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                    🏠 กลับหน้าหลัก
                </a>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">🏘️ จัดการหมู่บ้าน</h1>
            <p class="text-lg text-gray-600">ระบบจัดการข้อมูลหมู่บ้านในโครงการสมุนไพรท้องถิ่น</p>
        </div>

        <!-- Navigation Menu -->
        <div class="text-center mb-8">
            <a href="index.php" class="inline-block px-6 py-3 mx-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">หน้าหลัก</a>
            <a href="herb.php" class="inline-block px-6 py-3 mx-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">จัดการสมุนไพร</a>
            <a href="family-management.php" class="inline-block px-6 py-3 mx-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">จัดการตระกูลพืช</a>
            <a href="village-management.php" class="inline-block px-6 py-3 mx-2 bg-purple-700 text-white rounded-lg">จัดการหมู่บ้าน</a>
        </div>

        <!-- Message -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Village Form - Show only when action=create, action=update, or edit parameter exists -->
        <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'update') || isset($_GET['edit'])): ?>
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
                <?php echo $editVillage ? '✏️ แก้ไขข้อมูลหมู่บ้าน' : '➕ เพิ่มหมู่บ้านใหม่'; ?>
            </h2>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $editVillage ? 'update' : 'create'; ?>">
                <?php if ($editVillage): ?>
                    <input type="hidden" name="id" value="<?php echo $editVillage['id']; ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">ชื่อหมู่บ้าน *</label>
                        <input type="text" id="name" name="name" required
                            value="<?php echo $editVillage ? htmlspecialchars($editVillage['name']) : ''; ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="subDistrictId" class="block text-sm font-medium text-gray-700 mb-2">ตำบล</label>
                        <select id="subDistrictId" name="subDistrictId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- เลือกตำบล --</option>
                            <?php foreach ($subDistricts as $subDistrict): ?>
                                <option value="<?php echo $subDistrict['id']; ?>"
                                    <?php echo ($editVillage && $editVillage['subDistrictId'] == $subDistrict['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subDistrict['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="px-8 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors shadow-md font-medium">
                        <?php echo $editVillage ? '✅ อัปเดต' : '💾 บันทึก'; ?>
                    </button>
                    <?php if ($editVillage): ?>
                        <a href="village-management.php" class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors inline-block shadow-md font-medium">
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

        <!-- Add Village Button - Show only when form is not displayed -->
        <?php if (!isset($_GET['action']) && !isset($_GET['edit'])): ?>
        <div class="text-center mb-8">
            <a href="?action=create" class="inline-block px-8 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors shadow-md font-medium">
                ➕ เพิ่มหมู่บ้านใหม่
            </a>
        </div>
        <?php endif; ?>

        <!-- Villages List -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">📋 รายการหมู่บ้าน</h2>

            <?php if (empty($villages)): ?>
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🏘️</div>
                    <p class="text-gray-600 text-lg">ไม่มีข้อมูลหมู่บ้าน</p>
                    <p class="text-gray-500 text-sm mt-2">เริ่มต้นเพิ่มหมู่บ้านแรกของคุณ</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($villages as $village): ?>
                        <div class="flex justify-between items-center p-6 bg-gradient-to-r from-white to-gray-50 rounded-lg border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div>
                                <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($village['name']); ?></h3>
                                <p class="text-sm text-gray-600">
                                    ตำบล: <?php echo $village['sub_district_name'] ? htmlspecialchars($village['sub_district_name']) : 'ไม่ระบุ'; ?>
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="?edit=<?php echo $village['id']; ?>"
                                    class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors shadow-md font-medium">
                                    ✏️ แก้ไข
                                </a>
                                <form method="POST" class="inline" onsubmit="return confirm('คุณต้องการลบหมู่บ้าน &quot;<?php echo htmlspecialchars($village['name']); ?>&quot; หรือไม่?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $village['id']; ?>">
                                    <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors shadow-md font-medium">
                                        🗑️ ลบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>