<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

interface FileStorageInterface
{
    public function store(string $tempPath, string $extension): string;

    public function delete(string $key): void;

    public function exists(string $key): bool;

    public function getUrl(string $key): string;
}
