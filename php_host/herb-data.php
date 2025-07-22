<?php
require_once "connect.php";
$sql = "SELECT herbs.id, herbs.name, herbs.scientificName, families.name AS familyName 
        FROM herbs 
        JOIN families ON herbs.familyId = families.id
        ORDER BY herbs.id DESC";
$stmt = $pdo->query($sql);
$herbs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herb Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Noto Sans Thai', sans-serif;
    }
</style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center mb-4">Herb Data</h1>
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">รหัส</th>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">ชื่อ</th>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">คำอธิบาย</th>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">ชื่อวิทยาศาสตร์</th>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">ใช้งานอยู่</th>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">สร้างเมื่อ</th>
                <th class="py-2 px-4 border-b-2 border-gray-300 text-left leading-tight">อัปเดตเมื่อ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($herbs as $herb): ?>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($herb['id']); ?></td>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($herb['name']); ?></td>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($herb['description']); ?></td>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($herb['scientificName']); ?></td>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo $herb['isActive'] ? 'Yes' : 'No'; ?></td>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($herb['createdAt']); ?></td>
                    <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($herb['updatedAt']); ?></td>
                </tr>
            <?php endforeach; 
            if(count($herbs) < 1): ?>
                <tr>
                    <td colspan="8" class="py-2 px-4 border-b border-gray-200 text-center">ไม่พบข้อมูล</td>
                </tr>
            <?php endif; ?>
            ?>
        </tbody>
    </table>
    </div>
</body>
</html>
