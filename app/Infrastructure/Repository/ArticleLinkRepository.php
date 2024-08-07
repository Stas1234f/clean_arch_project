<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Dto\ArticleLinkDto;
use App\Domain\Repositories\ArticleLinkRepositoryInterface;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\LinkId;
use App\Infrastructure\Model\ArticleLink as ArticleLinkModel;
use Illuminate\Support\Collection;

class ArticleLinkRepository implements ArticleLinkRepositoryInterface
{
    public function create(ArticleId $articleId, LinkId $linkId): void
    {
        ArticleLinkModel::query()
            ->create([
                'article_id' => $articleId,
                'link_id' => $linkId,
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findByArticleId(ArticleId $id): array
    {
        $articleLinks = ArticleLinkModel::query()->where('article_id', $id->value())->get();

        return $this->mapToDtoArray($articleLinks);
    }

    /**
     * {@inheritDoc}
     */
    public function findByLinkId(LinkId $linkId): array
    {
        $articleLinks = ArticleLinkModel::query()->where('link_id', $linkId->value())->get();

        return $this->mapToDtoArray($articleLinks);
    }

    /**
     * @return ArticleLinkDto[]
     */
    private function mapToDtoArray(Collection $collection): array
    {
        return $collection->map(function (ArticleLinkModel $articleLink) {
            return new ArticleLinkDto(
                articleId: $articleLink->article_id,
                linkId: $articleLink->link_id,
            );
        })->all();
    }
}
