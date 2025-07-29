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

app.post("/save", async (c) => {
  const body = await c.req.json()
  console.log(body)
  return c.json(body)
})

export default {
  port: process.env.PORT || 3000,
  fetch: app.fetch,
}
