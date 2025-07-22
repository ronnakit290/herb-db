import { EntitySchema } from "typeorm";

const SubDistrict = new EntitySchema({
    name: "SubDistrict",
    tableName: "sub_districts",
    columns: {
        id: {
            primary: true,
            type: "int",
            generated: true
        },
        name: {
            type: "varchar",
            length: 255,
            comment: "ชื่อตำบล"
        },
    },
    relations: {
        villages: {
            type: "one-to-many",
            target: "Village",
            inverseSide: "sub_district",
            onDelete: "RESTRICT"
        }
    }
})

export default SubDistrict;