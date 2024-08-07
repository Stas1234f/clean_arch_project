<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

abstract class AbstractId
{
    public function __construct(private int $id)
    {
        $this->validate();
    }

    public function value(): int
    {
        return $this->id;
    }

    private function validate(): void
    {
        ($this->id > 0) ?? throw new InvalidArgumentException('need to be greater than zero');
    }
}
