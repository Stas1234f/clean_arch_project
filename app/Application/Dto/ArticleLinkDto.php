<?php

declare(strict_types=1);

namespace App\Application\Dto;

final readonly class ArticleLinkDto
{
    public function __construct(
        public int $articleId,
        public int $linkId
    ) {}
}
