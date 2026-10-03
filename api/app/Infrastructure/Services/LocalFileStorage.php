<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Ports\Outbound\FileStorageInterface;

final class LocalFileStorage implements FileStorageInterface
{
    private string $storagePath;

    public function __construct(?string $storagePath = null)
    {
        $this->storagePath = $storagePath ?? (string) env('MEDIA_ROOT', storage_path('app/media'));
        if (!is_dir($this->storagePath)) {
            @mkdir($this->storagePath, 0755, true);
        }
    }

    public function store(string $tempPath, string $extension): string
    {
        $cleanExt = ltrim(strtolower($extension), '.');
        $key = bin2hex(random_bytes(16)) . '.' . $cleanExt;
        $target = $this->storagePath . DIRECTORY_SEPARATOR . $key;

        copy($tempPath, $target);

        return $key;
    }

    public function delete(string $key): void
    {
        $path = $this->storagePath . DIRECTORY_SEPARATOR . $key;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public function exists(string $key): bool
    {
        return file_exists($this->storagePath . DIRECTORY_SEPARATOR . $key);
    }

    public function getUrl(string $key): string
    {
        return '/media/' . $key;
    }
}
