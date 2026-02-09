# php-aws-integration-demo

## PHP + AWS Integration Demo

This project is a small real-world style PHP REST API that demonstrates practical backend development and cloud-ready architecture.

The goal is to showcase:
- Clean REST API design  
- Environment-based configuration  
- Database integration  
- Cloud service integration (AWS-compatible)  
- Deployable backend service mindset  

This is not a tutorial project – this is a compact, realistic integration demo.

---

## 🚀 Architecture (target)

Client  
→ PHP REST API (EC2 / Docker)  
→ Database (RDS – MariaDB/MySQL)  
→ File storage (S3)  
→ Logs (CloudWatch)  

**Local development stack:**
- Docker Compose  
- MariaDB  
- MinIO (S3-compatible storage)

---

## 🔧 Tech stack

- PHP 8.x  
- Composer  
- MariaDB / MySQL  
- Docker & Docker Compose  
- AWS EC2 (target)  
- AWS RDS (target)  
- AWS S3 (target)  
- AWS IAM (target)  
- Nginx + PHP-FPM (production target)  
- CloudWatch (target logging)

---

## 📦 Features

- `GET /health` – service health check  
- `GET /bookings` – list bookings  
- `POST /bookings` – create booking  
- `POST /bookings/{id}/files` – upload file for booking  
- File storage in S3-compatible object storage (MinIO locally)  
- Presigned download URLs for uploaded files  
- Store booking data in database  
- Configuration via environment variables  
- Basic error handling & logging  

---

## ⚙️ Environment config

```bash
cp .env.example .env
