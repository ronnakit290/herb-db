import { Hono } from 'hono'
import { AppDataSource } from './config/database'
import dotenv from 'dotenv'
dotenv.config()

AppDataSource.initialize().then(() => {
  console.log('Database connected')
}).catch((error) => {
  console.error('Database connection error:', error)
})
const app = new Hono()

app.get('/', (c) => {
  return c.text('Hello Hono!')
})

export default {
  port: process.env.PORT || 3441,
  fetch: app.fetch,
}
