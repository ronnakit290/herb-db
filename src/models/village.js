import { EntitySchema } from "typeorm";

const Village = new EntitySchema({
    name: "Village",
    tableName: "villages",
    columns: {
        id: {
            primary: true,
            type: "int",
            generated: true
        },
        name: {
            type: "varchar",
            length: 255,
            comment: "ชื่อหมู่บ้าน"
        },
        villageNumber: {
            type: "varchar",
            length: 10,
            nullable: true,
            comment: "หมู่ที่"
        },
        sub_districtId: {
            type: "int",
            comment: "รหัสตำบล"
        }
    },
    relations: {
        sub_district: {
            type: "many-to-one",
            target: "SubDistrict",
            inverseSide: "villages",
            onDelete: "RESTRICT"
        },
        herbs: {
            type: "one-to-many",
            target: "Herb",
            inverseSide: "village",
            onDelete: "RESTRICT"
        },
        photos: {
            type: "one-to-many",
            target: "Photo",
            inverseSide: "village",
            onDelete: "CASCADE"
        }
    }
})

export default Village;