<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลหมู่บ้าน</title>
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
        input, textarea, select {
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
        .villages-list {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .village-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .village-info h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        .village-info p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .village-actions {
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏘️ จัดการข้อมูลหมู่บ้าน</h1>
        
        <div class="navigation">
            <a href="herb.php">จัดการสมุนไพร</a>
            <a href="family-management.php">จัดการตระกูลพืช</a>
            <a href="village-management.php">จัดการหมู่บ้าน</a>
        </div>
        
        <div id="message"></div>
        
        <div class="form-section">
            <h2 id="formTitle">เพิ่มหมู่บ้านใหม่</h2>
            <form id="villageForm">
                <input type="hidden" id="villageId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">ชื่อหมู่บ้าน *</label>
                        <input type="text" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="subDistrictId">ตำบล</label>
                        <select id="subDistrictId">
                            <option value="">-- เลือกตำบล --</option>
                        </select>
                    </div>
                </div>
                

                
                <button type="submit" class="btn btn-success">บันทึก</button>
                <button type="button" class="btn btn-danger" onclick="clearForm()">ล้างข้อมูล</button>
            </form>
        </div>
        
        <div class="villages-list">
            <h2>รายการหมู่บ้าน</h2>
            <button onclick="loadVillages()" class="btn btn-success">โหลดข้อมูล</button>
            <div id="villagesList"></div>
        </div>
    </div>

    <script>
        // Load villages and sub-districts on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadVillages();
            loadSubDistricts();
        });

        // Handle form submission
        document.getElementById('villageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const villageId = document.getElementById('villageId').value;
            const name = document.getElementById('name').value;
            const subDistrictId = document.getElementById('subDistrictId').value;
            
            const data = {
                name: name,
                subDistrictId: subDistrictId || null
            };
            
            if (villageId) {
                data.id = villageId;
                updateVillage(data);
            } else {
                createVillage(data);
            }
        });

        // Load sub-districts for dropdown
        function loadSubDistricts() {
            fetch('village-action.php?action=sub_districts')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const subDistrictSelect = document.getElementById('subDistrictId');
                    subDistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
                    
                    result.data.forEach(subDistrict => {
                        const option = document.createElement('option');
                        option.value = subDistrict.id;
                        option.textContent = subDistrict.name;
                        subDistrictSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.log('ไม่สามารถโหลดข้อมูลตำบลได้:', error);
            });
        }

        // Create new village
        function createVillage(data) {
            fetch('village-action.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage('เพิ่มหมู่บ้านสำเร็จ!', 'success');
                    clearForm();
                    loadVillages();
                } else {
                    showMessage('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                console.error('Error:', error);
            });
        }

        // Update village
        function updateVillage(data) {
            fetch('village-action.php?action=update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage('อัปเดตหมู่บ้านสำเร็จ!', 'success');
                    clearForm();
                    loadVillages();
                } else {
                    showMessage('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                console.error('Error:', error);
            });
        }

        // Load all villages
        function loadVillages() {
            fetch('village-action.php?action=read')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayVillages(result.data);
                } else {
                    showMessage('ไม่สามารถโหลดข้อมูลได้: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
                console.error('Error:', error);
            });
        }

        // Display villages list
        function displayVillages(villages) {
            const villagesList = document.getElementById('villagesList');
            
            if (villages.length === 0) {
                villagesList.innerHTML = '<p>ไม่มีข้อมูลหมู่บ้าน</p>';
                return;
            }
            
            villagesList.innerHTML = villages.map(village => `
                <div class="village-item">
                    <div class="village-info">
                        <h3>${village.name}</h3>
                        <p>ตำบล: ${village.sub_district_name || 'ไม่ระบุ'}</p>
                    </div>
                    <div class="village-actions">
                        <button class="btn btn-warning" onclick="editVillage(${village.id}, '${village.name}', ${village.subDistrictId || 'null'})">แก้ไข</button>
                        <button class="btn btn-danger" onclick="deleteVillage(${village.id}, '${village.name}')">ลบ</button>
                    </div>
                </div>
            `).join('');
        }

        // Edit village
        function editVillage(id, name, subDistrictId) {
            document.getElementById('villageId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('subDistrictId').value = subDistrictId || '';
            document.getElementById('formTitle').textContent = 'แก้ไขหมู่บ้าน';
            
            // Scroll to form
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Delete village
        function deleteVillage(id, name) {
            if (confirm(`คุณต้องการลบหมู่บ้าน "${name}" หรือไม่?`)) {
                fetch('village-action.php?action=delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showMessage('ลบหมู่บ้านสำเร็จ!', 'success');
                        loadVillages();
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
            document.getElementById('villageId').value = '';
            document.getElementById('name').value = '';
            document.getElementById('subDistrictId').value = '';
            document.getElementById('formTitle').textContent = 'เพิ่มหมู่บ้านใหม่';
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