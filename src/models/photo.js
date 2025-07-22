import { EntitySchema } from 'typeorm';

export const Photo = new EntitySchema({
    name: "Photo",
    tableName: "photos",
    columns: {
        id: {
            primary: true,
            type: "int",
            generated: true
        },
        filename: {
            type: "varchar",
            length: 255,
            comment: "ชื่อไฟล์รูปภาพ"
        },
        originalName: {
            type: "varchar",
            length: 255,
            nullable: true,
            comment: "ชื่อไฟล์ต้นฉบับ"
        },
        path: {
            type: "varchar",
            length: 500,
            comment: "เส้นทางไฟล์"
        },
        url: {
            type: "varchar",
            length: 500,
            nullable: true,
            comment: "URL ของรูปภาพ"
        },
        mimeType: {
            type: "varchar",
            length: 100,
            nullable: true,
            comment: "ประเภทไฟล์"
        },
        size: {
            type: "int",
            nullable: true,
            comment: "ขนาดไฟล์ (bytes)"
        },
        alt: {
            type: "varchar",
            length: 255,
            nullable: true,
            comment: "ข้อความทดแทนรูปภาพ"
        },
        description: {
            type: "text",
            nullable: true,
            comment: "คำอธิบายรูปภาพ"
        },
        herbId: {
            type: "int",
            nullable: true,
            comment: "รหัสสมุนไพร"
        },
        familyId: {
            type: "int",
            nullable: true,
            comment: "รหัสวงศ์พืช"
        },
        villageId: {
            type: "int",
            nullable: true,
            comment: "รหัสหมู่บ้าน"
        },
        isActive: {
            type: "boolean",
            default: true,
            comment: "สถานะการใช้งาน"
        },
        createdAt: {
            type: "timestamp",
            default: () => "CURRENT_TIMESTAMP",
            comment: "วันที่สร้าง"
        },
        updatedAt: {
            type: "timestamp",
            default: () => "CURRENT_TIMESTAMP",
            onUpdate: "CURRENT_TIMESTAMP",
            comment: "วันที่แก้ไข"
        }
    },
    relations: {
        herb: {
            type: "many-to-one",
            target: "Herb",
            inverseSide: "photos",
            onDelete: "CASCADE"
        },
        family: {
            type: "many-to-one",
            target: "Family",
            inverseSide: "photos",
            onDelete: "CASCADE"
        },
        village: {
            type: "many-to-one",
            target: "Village",
            inverseSide: "photos",
            onDelete: "CASCADE"
        }
    }
});

export default Photo;