# استخدام صورة PHP الرسمية مع Apache
FROM php:8.1-apache

# تفعيل نظام توجيه الروابط في أباتشي
RUN a2enmod rewrite

# تثبيت متطلبات أو امتدادات قاعدة البيانات إن وجدت (مثل pdo_mysql)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# نسخ ملفات المشروع إلى مجلد العمل الافتراضي في سيرفر أباتشي
COPY . /var/www/html/

# ضبط الصلاحيات (اختياري)
RUN chown -R www-data:www-data /var/www/html

# فتح البورت 80
EXPOSE 80