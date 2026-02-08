# php-aws-integration-demo

## PHP + AWS Integration Demo

This project is a small real-world style PHP REST API that demonstrates practical backend development and cloud-ready architecture.

The goal is to showcase:
- Clean REST API design  
- Environment-based configuration  
- Database integration  
- Cloud service integration (AWS)  
- Deployable backend service mindset  

This is not a tutorial project – this is a compact, realistic integration demo.

---

## 🚀 Architecture (target)

Client  
→ PHP REST API (EC2)  
→ Database (RDS - MariaDB/MySQL)  
→ File storage (S3)  
→ Logs (CloudWatch)  

(Local development uses XAMPP / MariaDB)

---

## 🔧 Tech stack

- PHP 8.x  
- Composer  
- MariaDB / MySQL  
- AWS EC2  
- AWS RDS  
- AWS S3  
- AWS IAM  
- Nginx + PHP-FPM (production target)  
- CloudWatch  

---

## 📦 Features

- `GET /health` – service health check  
- `GET /bookings` – list bookings  
- `POST /bookings` – create booking  
- File upload to AWS S3 (planned)  
- Store booking data in database  
- Configuration via environment variables  
- Basic error handling & logging  

---

## ⚙️ Environment config

```bash
cp .env.example .env
