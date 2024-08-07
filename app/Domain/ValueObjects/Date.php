<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use DateTime;
use InvalidArgumentException;

class Date
{
    public function __construct(private string $date)
    {
        $this->validate();
    }

    public function value(): string
    {
        return $this->date;
    }

    private function validate(): void
    {
        if (DateTime::createFromFormat('Y-m-d', $this->date) === false) {
            throw new InvalidArgumentException('Birthday value invalid');
        }
    }
}
