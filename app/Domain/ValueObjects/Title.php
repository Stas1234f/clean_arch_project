<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Title
{
    public function __construct(private string $title)
    {
        $this->validate();
    }

    public function value(): string
    {
        return $this->title;
    }

    private function validate(): void
    {
        (empty($this->title)) ?? throw new InvalidArgumentException('Title cannot be empty');
    }
}
