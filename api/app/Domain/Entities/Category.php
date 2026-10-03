<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use InvalidArgumentException;

final class Category
{
    private string $id;
    private string $name;

    public function __construct(string $id, string $name)
    {
        $trimmedName = trim($name);
        if ($trimmedName === '') {
            throw new InvalidArgumentException('El nombre de la categoría no puede estar vacío.');
        }

        $this->id = $id;
        $this->name = $trimmedName;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
