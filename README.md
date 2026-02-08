# php-aws-integration-demo

# PHP + AWS Integration Demo

This project is a simple PHP REST API deployed on AWS.  
The purpose of this demo is to showcase practical backend development and cloud fundamentals:

- PHP REST API
- AWS EC2 for hosting
- AWS RDS for database
- AWS S3 for file storage
- AWS IAM & environment variables
- AWS CloudWatch for logging

This is not a tutorial project – this is a small real-world style integration demo.

---

## 🚀 Architecture

Client  
→ PHP REST API (EC2)  
→ Database (RDS - MySQL/Postgres)  
→ File storage (S3)  
→ Logs (CloudWatch)

---

## 🔧 Tech stack

- PHP 8.x
- Composer
- AWS EC2
- AWS RDS
- AWS S3
- AWS IAM
- Nginx + PHP-FPM
- CloudWatch

---

## 📦 Features

- GET /health  
- GET /bookings  
- POST /bookings  
- Upload file to S3  
- Store booking data in RDS  
- Read configuration from environment variables  

---

## ⚙️ Setup (local)

```bash
composer install
cp .env.example .env
php -S localhost:8000 -t public
