FROM php:8.2-apache

# ติดตั้ง mysqli และ PDO MySQL
RUN apt-get update && docker-php-ext-install mysqli pdo pdo_mysql

# เปิด mod_rewrite (สำหรับ PHP framework หรือ router)
RUN a2enmod rewrite

# ตั้ง working directory
WORKDIR /var/www/html
