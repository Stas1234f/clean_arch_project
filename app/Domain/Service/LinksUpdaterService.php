<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Application\Dto\UpdateArticleLinksDto;
use App\Domain\Enum\LinkStatusEnum;
use App\Domain\Repositories\ArticleLinkRepositoryInterface;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\LinkId;
use App\Domain\ValueObjects\Status;
use App\Domain\ValueObjects\Url;

class LinksUpdaterService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly ArticleLinkRepositoryInterface $articleLinkRepository
    ) {}

    public function updateArticleLinks(UpdateArticleLinksDto $dto): void
    {
        $articleLinks = $this->articleLinkRepository->findByLinkId(new LinkId($dto->id));
        foreach ($articleLinks as $articleLink) {
            $article = $this->articleRepository->findById(new ArticleId($articleLink->articleId));
            if ($article !== null) {
                $article->updateContentLink(
                    new Url($dto->oldUrl),
                    new Url($dto->newUrl),
                    new Status(LinkStatusEnum::from($dto->status))
                );
                $this->articleRepository->update($article);
            }
        }
    }
}
