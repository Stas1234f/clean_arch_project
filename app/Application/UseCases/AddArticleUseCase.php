<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dto\CreateArticleDto;
use App\Application\Service\UrlExtractorService;
use App\Domain\Entities\Article;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Service\LinksEmbedderService;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Content;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\Title;

class AddArticleUseCase
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly UrlExtractorService $extractorService,
        private readonly LinksEmbedderService $articleLinksService,
    ) {}

    public function execute(CreateArticleDto $articleDto): int
    {
        $article = new Article(
            id: null,
            title: new Title($articleDto->title),
            publishedDate: new Date($articleDto->date),
            content: new Content($articleDto->content),
        );

        $id = $this->articleRepository->create($article);

        $urls = $this->extractorService->extractUrls($article->content()->value());
        $this->articleLinksService->addArticleLinks($urls, new ArticleId($id->value()));

        return $id->value();
    }
}
