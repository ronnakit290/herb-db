<?php
require_once 'connect.php';

$stats = [];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM herbs");
    $stats['herbs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM families");
    $stats['families'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM villages");
    $stats['villages'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("
        SELECT h.name, h.englishName, f.name as family_name, v.name as village_name 
        FROM herbs h 
        LEFT JOIN families f ON h.familyId = f.id 
        LEFT JOIN villages v ON h.villageId = v.id 
        ORDER BY h.id DESC 
        LIMIT 5
    ");
    $recentHerbs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $stats = ['herbs' => 0, 'families' => 0, 'villages' => 0];
    $recentHerbs = [];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสมุนไพร - หน้าหลัก</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">🌿 ระบบจัดการสมุนไพร</h1>
            <p class="text-lg text-gray-600">ระบบจัดการข้อมูลสมุนไพรท้องถิ่น</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center transform hover:scale-105 transition-transform duration-300">
                <div class="text-3xl mb-2">🌱</div>
                <h3 class="text-2xl font-bold text-green-600"><?php echo number_format($stats['herbs']); ?></h3>
                <p class="text-gray-600">สมุนไพรทั้งหมด</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 text-center transform hover:scale-105 transition-transform duration-300">
                <div class="text-3xl mb-2">🌳</div>
                <h3 class="text-2xl font-bold text-blue-600"><?php echo number_format($stats['families']); ?></h3>
                <p class="text-gray-600">ตระกูลพืช</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 text-center transform hover:scale-105 transition-transform duration-300">
                <div class="text-3xl mb-2">🏘️</div>
                <h3 class="text-2xl font-bold text-purple-600"><?php echo number_format($stats['villages']); ?></h3>
                <p class="text-gray-600">หมู่บ้าน</p>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <a href="herb.php" class="block bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300 group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🌿</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">จัดการสมุนไพร</h3>
                <p class="text-gray-600">เพิ่ม แก้ไข และจัดการข้อมูลสมุนไพร</p>
            </a>
            
            <a href="family-management.php" class="block bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300 group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🌳</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">จัดการตระกูลพืช</h3>
                <p class="text-gray-600">จัดการข้อมูลตระกูลและหมวดหมู่พืช</p>
            </a>
            
            <a href="village-management.php" class="block bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300 group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🏘️</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">จัดการหมู่บ้าน</h3>
                <p class="text-gray-600">จัดการข้อมูลหมู่บ้านและพื้นที่</p>
            </a>
        </div>
        
        <!-- Recent Herbs -->
        <?php if (!empty($recentHerbs)): ?>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">สมุนไพรที่เพิ่มล่าสุด</h2>
            <div class="space-y-4">
                <?php foreach ($recentHerbs as $herb): ?>
                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($herb['name']); ?></h3>
                        <div class="text-sm text-gray-600">
                            <?php if ($herb['englishName']): ?>
                                <span class="mr-4">ชื่อภาษาอังกฤษ: <?php echo htmlspecialchars($herb['englishName']); ?></span>
                            <?php endif; ?>
                            <?php if ($herb['family_name']): ?>
                                <span class="mr-4">ตระกูล: <?php echo htmlspecialchars($herb['family_name']); ?></span>
                            <?php endif; ?>
                            <?php if ($herb['village_name']): ?>
                                <span>หมู่บ้าน: <?php echo htmlspecialchars($herb['village_name']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-6 text-center">
                <a href="herb.php" class="inline-block bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors">
                    ดูสมุนไพรทั้งหมด
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="text-center mt-12 text-gray-600">
            <p>&copy; 2024 ระบบจัดการสมุนไพร - พัฒนาด้วย PHP Pure & Tailwind CSS</p>
        </div>
    </div>
</body>
</html>