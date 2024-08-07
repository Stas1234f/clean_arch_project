<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Enum\LinkStatusEnum;

class Status
{
    public function __construct(private LinkStatusEnum $status) {}

    public function value(): string
    {
        return $this->status->value;
    }
}
