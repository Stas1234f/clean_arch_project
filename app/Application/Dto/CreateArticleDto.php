<?php

declare(strict_types=1);

namespace App\Application\Dto;

final readonly class CreateArticleDto
{
    public function __construct(
        public string $title,
        public string $content,
        public string $date,
    ) {}
}
