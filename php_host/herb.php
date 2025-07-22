<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herb Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        *{
            font-family: 'Noto Sans Thai', sans-serif;
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
        textarea {
            height: 80px;
            resize: vertical;
        }
        button {
            background: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        button:hover {
            background: #2980b9;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #229954;
        }
        .herbs-list {
            margin-top: 30px;
        }
        .herb-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .herb-item h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        .herb-actions {
            margin-top: 10px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .village-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .village-card {
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .village-card:hover {
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .village-card.selected {
            border-color: #27ae60;
            background: #f8fff8;
        }
        .village-card h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .village-card p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .village-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .village-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .village-card:hover {
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
        }
        .village-card.selected {
            border-color: #2ecc71;
            background: #f8fff9;
        }
        .village-card h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .village-card p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .filter-status {
            background: #e8f5e8;
            border: 1px solid #2ecc71;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
            color: #27ae60;
            font-weight: bold;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌿 ระบบจัดการข้อมูลสมุนไพร</h1>
        
        <div id="message"></div>
        
        <div id="villageSelection">
            <h2>🏘️ เลือกหมู่บ้าน</h2>
            <div id="villageCards" class="village-cards"></div>
        </div>
        
        <div id="filterStatus"></div>
        
        <div id="formSection" class="form-section hidden">
            <h2>เพิ่ม/แก้ไขข้อมูลสมุนไพร</h2>
            <form id="herbForm">
                <input type="hidden" id="herbId" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">ชื่อสมุนไพรไทย *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="englishName">ชื่อภาษาอังกฤษ</label>
                        <input type="text" id="englishName" name="englishName">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="scientificName">ชื่อวิทยาศาสตร์</label>
                        <input type="text" id="scientificName" name="scientificName">
                    </div>
                    <div class="form-group">
                        <label for="familyId">วงศ์ของพืช</label>
                        <select id="familyId" name="familyId">
                            <option value="">-- เลือกวงศ์พืช --</option>
                        </select>
                    </div>
                </div>
                
                <input type="hidden" id="villageId" name="villageId">
                
                <div class="form-group">
                    <label for="description">คำอธิบายสมุนไพร</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <button type="submit" class="btn-success">บันทึก</button>
                <button type="button" onclick="clearForm()" class="btn-danger">ล้างข้อมูล</button>
                <button type="button" onclick="clearFilter()" class="btn-danger">กลับไปเลือกหมู่บ้าน</button>
            </form>
        </div>
        
        <div id="herbsSection" class="herbs-list hidden">
            <h2>รายการสมุนไพร</h2>
            <button onclick="loadHerbs()" class="btn-success">โหลดข้อมูล</button>
            <button onclick="clearFilter()" class="btn-danger">เลือกหมู่บ้านใหม่</button>
            <div id="herbsList"></div>
        </div>
    </div>

    <script>
        // Load herbs, families and villages on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFamilies();
            loadVillageCards();
            checkVillageSelection();
        });

        // Form submission
        document.getElementById('herbForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Remove empty values
            Object.keys(data).forEach(key => {
                if (data[key] === '' || data[key] === null) {
                    delete data[key];
                }
            });
            
            const action = data.id ? 'update' : 'create';
            
            fetch(`herb-action.php?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage(result.message, 'success');
                    clearForm();
                    loadHerbs();
                } else {
                    showMessage(result.error, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาด: ' + error.message, 'error');
            });
        });

        // Load all herbs
        function loadHerbs() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedVillage = urlParams.get('village');
            
            if (selectedVillage) {
                loadHerbsByVillage(selectedVillage);
            } else {
                fetch('herb-action.php?action=read')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        displayHerbs(result.data);
                    } else {
                        showMessage(result.error, 'error');
                    }
                })
                .catch(error => {
                    showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message, 'error');
                });
            }
        }
        
        // Load herbs by village
        function loadHerbsByVillage(villageId) {
            fetch(`herb-action.php?action=read&village=${villageId}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayHerbs(result.data);
                } else {
                    showMessage(result.error, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message, 'error');
            });
        }

        // Display herbs list
        function displayHerbs(herbs) {
            const herbsList = document.getElementById('herbsList');
            
            if (herbs.length === 0) {
                herbsList.innerHTML = '<p>ไม่มีข้อมูลสมุนไพร</p>';
                return;
            }
            
            herbsList.innerHTML = herbs.map(herb => `
                <div class="herb-item">
                    <h3>${herb.name}</h3>
                    <p><strong>ชื่อภาษาอังกฤษ:</strong> ${herb.englishName || 'ไม่ระบุ'}</p>
                    <p><strong>ชื่อวิทยาศาสตร์:</strong> ${herb.scientificName || 'ไม่ระบุ'}</p>
                    <p><strong>วงศ์:</strong> ${herb.family_name || 'ไม่ระบุ'}</p>
                    <p><strong>หมู่บ้าน:</strong> ${herb.village_name || 'ไม่ระบุ'}</p>
                    <p><strong>คำอธิบาย:</strong> ${herb.description || 'ไม่มีคำอธิบาย'}</p>
                    <div class="herb-actions">
                        <button onclick="editHerb(${herb.id})" class="btn-success">แก้ไข</button>
                        <button onclick="deleteHerb(${herb.id})" class="btn-danger">ลบ</button>
                    </div>
                </div>
            `).join('');
        }

        // Edit herb
        function editHerb(id) {
            fetch(`herb-action.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const herb = result.data;
                    document.getElementById('herbId').value = herb.id;
                    document.getElementById('name').value = herb.name;
                    document.getElementById('englishName').value = herb.englishName || '';
                    document.getElementById('scientificName').value = herb.scientificName || '';
                    document.getElementById('familyId').value = herb.familyId || '';
                    document.getElementById('villageId').value = herb.villageId || '';
                    document.getElementById('description').value = herb.description || '';
                    
                    // Scroll to form
                    document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
                } else {
                    showMessage(result.error, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message, 'error');
            });
        }

        // Delete herb
        function deleteHerb(id) {
            if (!confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')) {
                return;
            }
            
            fetch(`herb-action.php?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage(result.message, 'success');
                    loadHerbs();
                } else {
                    showMessage(result.error, 'error');
                }
            })
            .catch(error => {
                showMessage('เกิดข้อผิดพลาด: ' + error.message, 'error');
            });
        }

        // Clear form
        function clearForm() {
            document.getElementById('herbForm').reset();
            document.getElementById('herbId').value = '';
        }

        // Load families for dropdown
        function loadFamilies() {
            fetch('../src/index.js')
            .then(() => {
                // Since we don't have a direct PHP endpoint for families,
                // we'll create a simple query here
                return fetch('herb-action.php?action=families');
            })
            .catch(() => {
                // Fallback: create families endpoint in herb-action.php
                return fetch('herb-action.php?action=families');
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const familySelect = document.getElementById('familyId');
                    familySelect.innerHTML = '<option value="">-- เลือกวงศ์พืช --</option>';
                    
                    result.data.forEach(family => {
                        const option = document.createElement('option');
                        option.value = family.id;
                        option.textContent = family.name;
                        familySelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.log('ไม่สามารถโหลดข้อมูลวงศ์พืชได้:', error);
            });
        }
        
        // Load villages as cards
        function loadVillageCards() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedVillage = urlParams.get('village');
            
            fetch('herb-action.php?action=villages')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const villageCardsContainer = document.getElementById('villageCards');
                    
                    villageCardsContainer.innerHTML = result.data.map(village => `
                        <div class="village-card ${selectedVillage == village.id ? 'selected' : ''}" 
                             onclick="selectVillage(${village.id}, '${village.name}')">
                            <h3>${village.name}</h3>
                            <p>คลิกเพื่อดูสมุนไพรในหมู่บ้านนี้</p>
                        </div>
                    `).join('');
                }
            })
            .catch(error => {
                console.log('ไม่สามารถโหลดข้อมูลหมู่บ้านได้:', error);
            });
        }
        
        // Select village
        function selectVillage(villageId, villageName) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('village', villageId);
            window.location.href = currentUrl.toString();
        }
        
        // Check village selection and show/hide sections
        function checkVillageSelection() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedVillage = urlParams.get('village');
            
            const villageSelection = document.getElementById('villageSelection');
            const formSection = document.getElementById('formSection');
            const herbsSection = document.getElementById('herbsSection');
            
            if (selectedVillage) {
                // Hide village selection, show form and herbs
                villageSelection.classList.add('hidden');
                formSection.classList.remove('hidden');
                herbsSection.classList.remove('hidden');
                
                // Set village ID in hidden input
                document.getElementById('villageId').value = selectedVillage;
                
                // Load herbs and update status
                loadHerbsByVillage(selectedVillage);
                updateFilterStatus();
            } else {
                // Show village selection, hide form and herbs
                villageSelection.classList.remove('hidden');
                formSection.classList.add('hidden');
                herbsSection.classList.add('hidden');
            }
        }

        // Update filter status display
        function updateFilterStatus() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedVillage = urlParams.get('village');
            const filterStatusDiv = document.getElementById('filterStatus');
            
            if (selectedVillage) {
                // Get village name from dropdown
                const villageSelect = document.getElementById('villageId');
                const selectedOption = villageSelect.querySelector(`option[value="${selectedVillage}"]`);
                const villageName = selectedOption ? selectedOption.textContent : `ID: ${selectedVillage}`;
                
                filterStatusDiv.innerHTML = `<div class="message success">กำลังแสดงสมุนไพรจากหมู่บ้าน: ${villageName}</div>`;
            } else {
                filterStatusDiv.innerHTML = '';
            }
        }
        
        // Clear village filter (go back to village selection)
        function clearFilter() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('village');
            window.location.href = currentUrl.toString();
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