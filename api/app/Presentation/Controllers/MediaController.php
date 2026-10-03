<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\Ports\Outbound\FileStorageInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class MediaController
{
    public function __construct(
        private readonly FileStorageInterface $fileStorage
    ) {}

    public function show(string $key): Response
    {
        $storagePath = (string) env('MEDIA_ROOT', storage_path('app/media'));
        $filePath = $storagePath . DIRECTORY_SEPARATOR . basename($key);

        if (!file_exists($filePath) || is_dir($filePath)) {
            // E-15 / P-31: 404 with empty body
            return response('', 404, ['Content-Length' => '0']);
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
        ];

        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

        return new BinaryFileResponse($filePath, 200, [
            'Content-Type' => $contentType,
        ]);
    }
}
