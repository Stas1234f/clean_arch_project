<?php

namespace App\Domain\Repositories;

use App\Application\Dto\ArticleLinkDto;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\LinkId;

interface ArticleLinkRepositoryInterface
{
    public function create(ArticleId $articleId, LinkId $linkId): void;

    /**
     * @return ArticleLinkDto[]
     */
    public function findByArticleId(ArticleId $id): array;

    /**
     * @return ArticleLinkDto[]
     */
    public function findByLinkId(LinkId $linkId): array;
}
