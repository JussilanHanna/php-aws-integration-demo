<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database\Db;
use App\Services\S3Service;
use App\Support\Http;

final class BookingController
{
    public function health(): void
    {
        Http::json([
            'ok' => true,
            'time' => gmdate('c')
        ]);
    }

    public function listBookings(): void
    {
        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT id, name, created_at FROM bookings ORDER BY id DESC LIMIT 200");
        $rows = $stmt->fetchAll();

        Http::json(['items' => $rows]);
    }

    public function createBooking(): void
    {
        $body = Http::bodyJson();
        $name = trim((string)($body['name'] ?? ''));

        if ($name === '') {
            Http::error('Field "name" is required', 422);
        }

        $pdo = Db::pdo();
        $stmt = $pdo->prepare("INSERT INTO bookings (name) VALUES (:name)");
        $stmt->execute([':name' => $name]);

        $id = (int)$pdo->lastInsertId();

        Http::json(['id' => $id, 'name' => $name], 201);
    }

    /**
     * POST /bookings/upload (multipart/form-data)
     * fields:
     *  - bookingId (int)
     *  - file (binary)
     */
    public function uploadFile(): void
    {
        $bookingId = isset($_POST['bookingId']) ? (int)$_POST['bookingId'] : 0;
        if ($bookingId <= 0) {
            Http::error('Field "bookingId" is required', 422);
        }

        if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
            Http::error('Field "file" is required (multipart/form-data)', 422);
        }

        $file = $_FILES['file'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Http::error('File upload failed', 400, ['code' => (int)($file['error'] ?? -1)]);
        }

        $tmpPath = (string)$file['tmp_name'];
        $originalName = (string)$file['name'];
        $mimeType = (string)($file['type'] ?? 'application/octet-stream');
        $sizeBytes = (int)($file['size'] ?? 0);

        // Verify booking exists
        $pdo = Db::pdo();
        $check = $pdo->prepare("SELECT id FROM bookings WHERE id = :id");
        $check->execute([':id' => $bookingId]);
        if (!$check->fetch()) {
            Http::error('Booking not found', 404, ['bookingId' => $bookingId]);
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName) ?: 'upload.bin';
        $key = "bookings/{$bookingId}/" . gmdate('Ymd_His') . "_" . $safeName;

        $s3 = new S3Service();
        $upload = $s3->upload($key, $tmpPath, $mimeType);

        // Store metadata to DB
        $stmt = $pdo->prepare("
            INSERT INTO booking_files (booking_id, s3_key, original_name, mime_type, size_bytes)
            VALUES (:booking_id, :s3_key, :original_name, :mime_type, :size_bytes)
        ");
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':s3_key' => $key,
            ':original_name' => $originalName,
            ':mime_type' => $mimeType,
            ':size_bytes' => $sizeBytes,
        ]);

        Http::json([
            'ok' => true,
            'bookingId' => $bookingId,
            'file' => [
                'originalName' => $originalName,
                'mimeType' => $mimeType,
                'sizeBytes' => $sizeBytes,
                's3' => $upload
            ]
        ], 201);
    }
}
