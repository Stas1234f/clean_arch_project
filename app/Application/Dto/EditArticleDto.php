<?php

declare(strict_types=1);

namespace App\Application\Dto;

final readonly class EditArticleDto
{
    public function __construct(
        public int $id,
        public string $content
    ) {}
}
