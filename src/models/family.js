import { EntitySchema } from 'typeorm';

export const Family = new EntitySchema({
    name: "Family",
    tableName: "families",
    columns: {
        id: {
            primary: true,
            type: "int",
            generated: true
        },
        name: {
            type: "varchar",
            length: 255
        },
        description: {
            type: "text",
            nullable: true
        },
        scientificName: {
            type: "varchar",
            length: 255,
            nullable: true
        },
        isActive: {
            type: "boolean",
            default: true
        },
        createdAt: {
            type: "timestamp",
            default: () => "CURRENT_TIMESTAMP"
        },
        updatedAt: {
            type: "timestamp",
            default: () => "CURRENT_TIMESTAMP",
            onUpdate: "CURRENT_TIMESTAMP"
        }
    },
    relations: {
        herbs: {
            type: "one-to-many",
            target: "Herb",
            inverseSide: "family",
            onDelete: "RESTRICT"
        },
        photos: {
            type: "one-to-many",
            target: "Photo",
            inverseSide: "family",
            onDelete: "CASCADE"
        }
    }
});

export default Family;
