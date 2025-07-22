<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลตระกูลพืช</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            transition: background-color 0.3s;
        }
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background-color: #219a52;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .btn-warning {
            background-color: #f39c12;
            color: white;
        }
        .btn-warning:hover {
            background-color: #d68910;
        }
        .families-list {
            margin-top: 30px;
        }
        .family-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .family-info h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        .family-info p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .family-actions {
            display: flex;
            gap: 10px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .navigation {
            text-align: center;
            margin-bottom: 20px;
        }
        .navigation a {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navigation a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌱 จัดการข้อมูลตระกูลพืช</h1>
        
        <div class="navigation">
            <a href="herb.php">จัดการสมุนไพร</a>
            <a href="family-management.php">จัดการตระกูลพืช</a>
            <a href="village-management.php">จัดการหมู่บ้าน</a>
        </div>
        
        <div id="message"></div>
        
        <div class="form-section">
            <h2 id="formTitle">เพิ่มตระกูลพืชใหม่</h2>
            <form id="familyForm">
                <input type="hidden" id="familyId">
                
                <div class="form-group">
                    <label for="name">ชื่อตระกูลพืช *</label>
                    <input type="text" id="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">คำอธิบาย</label>
                    <textarea id="description" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">บันทึก</button>
                <button type="button" class="btn btn-danger" onclick="clearForm()">ล้างข้อมูล</button>
            </form>
        </div>
        
        <div class="families-list">
            <h2>รายการตระกูลพืช</h2>
            <button onclick="loadFamilies()" class="btn btn-success">โหลดข้อมูล</button>
            <div id="familiesList"></div>
        </div>
    </div>

    <script>
        // Load families on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFamilies();
        });

        // Handle form submission
        document.getElementById('familyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const familyId = document.getElementById('familyId').value;
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            
            const data = {
                name: name,
                description: description
            };
            
            if (familyId) {
                data.id = familyId;
                updateFamily(data);
            } else {
                createFamily(data);
            }
        });

        // Create new family
        function createFamily(data) {
            fetch('family-action.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage('เพิ่มตระกูลพืชสำเร็จ!', 'success');
                    clearForm();
                    loadFamilies();
                } else {
                    showMessage('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                console.error('Error:', error);
            });
        }

        // Update family
        function updateFamily(data) {
            fetch('family-action.php?action=update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage('อัปเดตตระกูลพืชสำเร็จ!', 'success');
                    clearForm();
                    loadFamilies();
                } else {
                    showMessage('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                console.error('Error:', error);
            });
        }

        // Load all families
        function loadFamilies() {
            fetch('family-action.php?action=read')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayFamilies(result.data);
                } else {
                    showMessage('ไม่สามารถโหลดข้อมูลได้: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
                console.error('Error:', error);
            });
        }

        // Display families list
        function displayFamilies(families) {
            const familiesList = document.getElementById('familiesList');
            
            if (families.length === 0) {
                familiesList.innerHTML = '<p>ไม่มีข้อมูลตระกูลพืช</p>';
                return;
            }
            
            familiesList.innerHTML = families.map(family => `
                <div class="family-item">
                    <div class="family-info">
                        <h3>${family.name}</h3>
                        <p>${family.description || 'ไม่มีคำอธิบาย'}</p>
                    </div>
                    <div class="family-actions">
                        <button class="btn btn-warning" onclick="editFamily(${family.id}, '${family.name}', '${family.description || ''}')">แก้ไข</button>
                        <button class="btn btn-danger" onclick="deleteFamily(${family.id}, '${family.name}')">ลบ</button>
                    </div>
                </div>
            `).join('');
        }

        // Edit family
        function editFamily(id, name, description) {
            document.getElementById('familyId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('formTitle').textContent = 'แก้ไขตระกูลพืช';
            
            // Scroll to form
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Delete family
        function deleteFamily(id, name) {
            if (confirm(`คุณต้องการลบตระกูลพืช "${name}" หรือไม่?`)) {
                fetch('family-action.php?action=delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showMessage('ลบตระกูลพืชสำเร็จ!', 'success');
                        loadFamilies();
                    } else {
                        showMessage('เกิดข้อผิดพลาด: ' + result.message, 'error');
                    }
                })
                .catch(error => {
                    showMessage('เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                    console.error('Error:', error);
                });
            }
        }

        // Clear form
        function clearForm() {
            document.getElementById('familyForm').reset();
            document.getElementById('familyId').value = '';
            document.getElementById('formTitle').textContent = 'เพิ่มตระกูลพืชใหม่';
        }

        // Show message
        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = `<div class="message ${type}">${message}</div>`;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }
    </script>
</body>
</html>