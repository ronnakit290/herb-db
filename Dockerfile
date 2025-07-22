# ใช้ PHP 8.2 พร้อม Apache
FROM php:8.2-apache

# เปิดใช้งาน mod_rewrite (สำคัญถ้าใช้ Laravel หรือ .htaccess)
RUN a2enmod rewrite

# ติดตั้ง PDO และ PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# กำหนดสิทธิ์ให้โฟลเดอร์เว็บ
WORKDIR /var/www/html

# คัดลอก source code เฉพาะใช้ตอน build (ถ้าใช้ volume จะถูกแทนที่)
COPY ./src /var/www/html

# เปิดพอร์ต Apache
EXPOSE 80
