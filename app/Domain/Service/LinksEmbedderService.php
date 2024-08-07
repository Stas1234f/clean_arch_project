<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entities\Link;
use App\Domain\Enum\LinkStatusEnum;
use App\Domain\Repositories\ArticleLinkRepositoryInterface;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\Status;
use App\Domain\ValueObjects\Url;

class LinksEmbedderService
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
        private readonly ArticleLinkRepositoryInterface $articleLinkRepository,
    ) {}

    public function addArticleLinks(array $urls, ArticleId $articleId): void
    {
        foreach ($urls as $url) {
            $existingLink = $this->linkRepository->findByUrl(new Url($url));
            if ($existingLink === null) {
                $linkId = $this->linkRepository->create(
                    new Link(
                        linkId: null,
                        url: new Url($url),
                        status: new Status(LinkStatusEnum::OK),
                        dateActualiztion: new Date(date('Y-m-d')),
                    )
                );
                $this->articleLinkRepository->create($articleId, $linkId);
            }
        }
    }
}
