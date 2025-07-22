import { EntitySchema } from 'typeorm';

export const Herb = new EntitySchema({
    name: "Herb",
    tableName: "herbs",
    columns: {
        id: {
            primary: true,
            type: "int",
            generated: true
        },
        name: {
            type: "varchar",
            length: 255,
            comment: "ชื่อสมุนไพรไทย"
        },
        englishName: {
            type: "varchar",
            length: 255,
            nullable: true,
            comment: "ชื่อภาษาอังกฤษ"
        },
        description: {
            type: "text",
            nullable: true,
            comment: "คำอธิบายสมุนไพร"
        },
        scientificName: {
            type: "varchar",
            length: 255,
            nullable: true,
            comment: "ชื่อวิทยาศาสตร์"
        },
        familyId: {
            type: "int",
            nullable: true,
            comment: "วงศ์ของพืช"
        },
        villageId: {
            type: "int",
            nullable: true,
            comment: "หมู่บ้าน"
        },
    },
    relations: {
        family: {
            type: "many-to-one",
            target: "Family",
            inverseSide: "herbs",
            onDelete: "RESTRICT"
        },
        village: {
            type: "many-to-one",
            target: "Village",
            inverseSide: "herbs",
            onDelete: "RESTRICT"
        },
        photos: {
            type: "one-to-many",
            target: "Photo",
            inverseSide: "herb",
            onDelete: "CASCADE"
        }
    }
});
// Cascade คือ การลบข้อมูลที่เกี่ยวข้องด้วย
// No Action คือ ไม่ทำอะไร
// Set Null คือ กำหนดให้ค่าเป็น null
// Restrict คือ ไม่อนุญาติการลบ
// Set Default คือ กำหนดให้ค่าเป็นค่าเริ่มต้น

export default Herb;