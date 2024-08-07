<?php

declare(strict_types=1);

namespace App\Application\Dto;

final readonly class ActualizeLinkDto
{
    public function __construct(
        public int $id,
        public string $url,
    ) {}
}
