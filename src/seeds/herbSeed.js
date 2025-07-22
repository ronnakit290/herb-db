import { AppDataSource } from '../config/database.js';
import SubDistrict from '../models/sub_district.js';
import Village from '../models/village.js';
import Family from '../models/family.js';
import Herb from '../models/herb.js';

export async function seedHerbData() {
    try {
        console.log('🌱 เริ่มต้น seeding ข้อมูลสมุนไพร...');
        
        // สร้างข้อมูลตำบล
        const subDistrictRepo = AppDataSource.getRepository('SubDistrict');
        const subDistricts = await subDistrictRepo.save([
            { name: 'บ้านโป่ง' },
            { name: 'วังทอง' },
            { name: 'ดอนตูม' }
        ]);
        console.log('✅ สร้างข้อมูลตำบลเรียบร้อย');

        // สร้างข้อมูลหมู่บ้าน
        const villageRepo = AppDataSource.getRepository('Village');
        const villages = await villageRepo.save([
            { name: 'บ้านโป่งใต้', villageNumber: '1', sub_districtId: subDistricts[0].id },
            { name: 'บ้านโป่งเหนือ', villageNumber: '2', sub_districtId: subDistricts[0].id },
            { name: 'บ้านวังทอง', villageNumber: '3', sub_districtId: subDistricts[1].id },
            { name: 'บ้านดอนตูม', villageNumber: '4', sub_districtId: subDistricts[2].id },
            { name: 'บ้านหนองบัว', villageNumber: '5', sub_districtId: subDistricts[1].id }
        ]);
        console.log('✅ สร้างข้อมูลหมู่บ้านเรียบร้อย');

        // สร้างข้อมูลวงศ์พืช
        const familyRepo = AppDataSource.getRepository('Family');
        const families = await familyRepo.save([
            { 
                name: 'วงศ์ขิง', 
                scientificName: 'Zingiberaceae',
                description: 'วงศ์พืชที่มีรากเหง้าหอม เช่น ขิง ข่า กระชาย'
            },
            { 
                name: 'วงศ์ถั่ว', 
                scientificName: 'Fabaceae',
                description: 'วงศ์พืชที่มีผลเป็นฝัก เช่น มะขาม กระถิน'
            },
            { 
                name: 'วงศ์ส้ม', 
                scientificName: 'Rutaceae',
                description: 'วงศ์พืชที่มีใบหอม เช่น มะกรูด ส้มซ่า'
            },
            { 
                name: 'วงศ์กะเพรา', 
                scientificName: 'Lamiaceae',
                description: 'วงศ์พืชที่มีใบหอม เช่น กะเพรา โหระพา'
            },
            { 
                name: 'วงศ์ยาง', 
                scientificName: 'Euphorbiaceae',
                description: 'วงศ์พืชที่มีน้ำยางขาว เช่น ละหุ่ง สบู่ดำ'
            }
        ]);
        console.log('✅ สร้างข้อมูลวงศ์พืชเรียบร้อย');

        // สร้างข้อมูลสมุนไพร 5 รายการ
        const herbRepo = AppDataSource.getRepository('Herb');
        const herbs = await herbRepo.save([
            {
                name: 'ขิงแดง',
                englishName: 'Red Ginger',
                scientificName: 'Zingiber officinale var. rubrum',
                description: 'สมุนไพรที่มีรสเผ็ดร้อน ใช้แก้ท้องอืด ท้องเฟ้อ และช่วยขับลม',
                familyId: families[0].id,
                villageId: villages[0].id
            },
            {
                name: 'มะขามป้อม',
                englishName: 'Sweet Tamarind',
                scientificName: 'Phyllanthus acidus',
                description: 'ผลรสเปรียว ใช้ทำยาแก้ไข้ แก้กระหาย และช่วยย่อย',
                familyId: families[1].id,
                villageId: villages[1].id
            },
            {
                name: 'ใบมะกรูด',
                englishName: 'Kaffir Lime Leaves',
                scientificName: 'Citrus hystrix',
                description: 'ใบหอม ใช้ปรุงอาหาร และใช้เป็นยาแก้ไข้ แก้หวัด',
                familyId: families[2].id,
                villageId: villages[2].id
            },
            {
                name: 'กะเพราป่า',
                englishName: 'Wild Holy Basil',
                scientificName: 'Ocimum tenuiflorum',
                description: 'สมุนไพรที่มีกลิ่นหอมเฉพาะ ใช้แก้ไข้ แก้หวัด และช่วยขับเสมหะ',
                familyId: families[3].id,
                villageId: villages[3].id
            },
            {
                name: 'สบู่ดำ',
                englishName: 'Black Soap Tree',
                scientificName: 'Diospyros mollis',
                description: 'ผลสีดำ รสหวานเปรี้ยว ใช้แก้ไอ แก้เสมหะ และบำรุงปอด',
                familyId: families[4].id,
                villageId: villages[4].id
            }
        ]);
        console.log('✅ สร้างข้อมูลสมุนไพรเรียบร้อย');

        console.log(`🎉 Seeding เสร็จสิ้น! สร้างข้อมูล:`);
        console.log(`   - ตำบล: ${subDistricts.length} รายการ`);
        console.log(`   - หมู่บ้าน: ${villages.length} รายการ`);
        console.log(`   - วงศ์พืช: ${families.length} รายการ`);
        console.log(`   - สมุนไพร: ${herbs.length} รายการ`);
        
        return {
            subDistricts,
            villages,
            families,
            herbs
        };
    } catch (error) {
        console.error('❌ เกิดข้อผิดพลาดในการ seed ข้อมูล:', error);
        throw error;
    }
}

// ฟังก์ชันสำหรับรัน seed แบบ standalone
export async function runSeed() {
    try {
        if (!AppDataSource.isInitialized) {
            await AppDataSource.initialize();
            console.log('📊 เชื่อมต่อฐานข้อมูลเรียบร้อย');
        }
        
        await seedHerbData();
        
        console.log('🏁 การ seed ข้อมูลเสร็จสมบูรณ์!');
    } catch (error) {
        console.error('💥 เกิดข้อผิดพลาด:', error);
        process.exit(1);
    }
}

// รัน seed ถ้าไฟล์นี้ถูกเรียกโดยตรง
if (import.meta.main) {
    runSeed();
}