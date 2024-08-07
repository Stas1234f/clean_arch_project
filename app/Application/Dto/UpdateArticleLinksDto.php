<?php

declare(strict_types=1);

namespace App\Application\Dto;

final readonly class UpdateArticleLinksDto
{
    public function __construct(
        public int $id,
        public string $oldUrl,
        public string $newUrl,
        public string $status
    ) {}
}
