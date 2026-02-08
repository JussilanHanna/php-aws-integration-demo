<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\Env;
use Aws\S3\S3Client;

final class S3Service
{
    private S3Client $client;
    private string $bucket;

    public function __construct()
    {
        $region = Env::get('AWS_REGION');
        $bucket = Env::get('AWS_S3_BUCKET');

        if (!$region || !$bucket) {
            throw new \RuntimeException('AWS_REGION and AWS_S3_BUCKET must be set');
        }

        $this->bucket = $bucket;

        // AWS SDK will use env vars, instance role, or credentials file automatically.
        $this->client = new S3Client([
            'version' => 'latest',
            'region'  => $region,
        ]);
    }

    public function upload(string $key, string $localFilePath, string $mimeType): array
    {
        $result = $this->client->putObject([
            'Bucket'      => $this->bucket,
            'Key'         => $key,
            'SourceFile'  => $localFilePath,
            'ContentType' => $mimeType,
        ]);

        return [
            'bucket' => $this->bucket,
            'key'    => $key,
            'etag'   => (string)($result['ETag'] ?? ''),
        ];
    }
}
