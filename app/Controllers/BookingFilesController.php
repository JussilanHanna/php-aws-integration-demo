<?php

namespace App\Controllers;

use App\Services\S3;
use PDO;

final class BookingFilesController
{
    public function upload(array $params)
    {
        $bookingId = (int)($params['id'] ?? 0);
        if ($bookingId <= 0) {
            return $this->json(400, ['error' => 'Invalid booking id']);
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(400, ['error' => 'Missing file (multipart field name: file)']);
        }

        $bucket = trim(getenv('S3_BUCKET') ?: 'demo-bucket', '/');

        $tmpPath      = $_FILES['file']['tmp_name'];
        $originalName = $_FILES['file']['name'];
        $mimeType     = $_FILES['file']['type'] ?: 'application/octet-stream';
        $sizeBytes    = (int)$_FILES['file']['size'];

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $key = "bookings/{$bookingId}/" . uniqid('file_', true) . "_" . $safeName;

        $s3 = S3::clientFromEnv();

        // Upload to S3 (MinIO)
        $s3->putObject([
            'Bucket'      => $bucket,
            'Key'         => $key,
            'Body'        => fopen($tmpPath, 'rb'),
            'ContentType' => $mimeType,
        ]);

        // Save metadata to DB
        $pdo = $this->pdo();
        $stmt = $pdo->prepare("
            INSERT INTO booking_files (booking_id, s3_key, original_name, mime_type, size_bytes)
            VALUES (:booking_id, :s3_key, :original_name, :mime_type, :size_bytes)
        ");
        $stmt->execute([
            ':booking_id'    => $bookingId,
            ':s3_key'        => $key,
            ':original_name' => $originalName,
            ':mime_type'     => $mimeType,
            ':size_bytes'    => $sizeBytes,
        ]);

        $fileId = (int)$pdo->lastInsertId();

        // Create presigned download URL (works in browser)
        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $key,
        ]);

        $request   = $s3->createPresignedRequest($cmd, '+15 minutes');
        $publicUrl = (string)$request->getUri();

        // Replace Docker internal host (minio) with host-accessible URL
        $publicBase = rtrim(getenv('S3_PUBLIC_BASE') ?: 'http://localhost:9000', '/');
        $publicUrl  = preg_replace('#^https?://[^/]+#', $publicBase, $publicUrl);

        return $this->json(201, [
            'id'         => $fileId,
            'booking_id'=> $bookingId,
            's3_key'    => $key,
            'url'       => $publicUrl,
        ]);
    }

    private function pdo(): PDO
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT') ?: '3306';
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    private function json(int $status, array $data)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return null;
    }
}
