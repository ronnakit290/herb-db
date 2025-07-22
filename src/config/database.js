import { DataSource } from 'typeorm';

const AppDataSource = new DataSource({
    type: "mysql",
    url: process.env.DATABASE_URL,
    synchronize: true,
    logging: false,
    entities: [
        "src/models/**/*.js"
    ],
    migrations: [
        "src/migration/**/*.js"
    ],
    subscribers: [
        "src/subscriber/**/*.js"
    ],
});

export { AppDataSource };
