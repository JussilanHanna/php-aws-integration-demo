<?php

namespace App\Services;

use Aws\S3\S3Client;

final class S3
{
    public static function clientFromEnv(): S3Client
    {
        $endpoint = getenv('S3_ENDPOINT') ?: 'http://minio:9000';
        $region   = getenv('S3_REGION') ?: 'us-east-1';

        return new S3Client([
            'version' => 'latest',
            'region'  => $region,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => (getenv('S3_PATH_STYLE') === 'true' || getenv('S3_PATH_STYLE') === '1'),
            'credentials' => [
                'key'    => getenv('S3_ACCESS_KEY') ?: 'minioadmin',
                'secret' => getenv('S3_SECRET_KEY') ?: 'minioadmin123',
            ],
            'http' => [
                'verify' => false,
            ],
        ]);
    }
}
