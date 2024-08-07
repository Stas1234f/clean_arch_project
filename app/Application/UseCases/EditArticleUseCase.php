<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dto\EditArticleDto;
use App\Application\Service\UrlExtractorService;
use App\Domain\Exception\ArticleNotFoundException;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Service\LinksEmbedderService;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Content;

class EditArticleUseCase
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly UrlExtractorService $extractorService,
        private readonly LinksEmbedderService $articleLinksService
    ) {}

    /**
     * @throws ArticleNotFoundException
     */
    public function execute(EditArticleDto $articleDto): int
    {
        $article = $this->articleRepository->findById(new ArticleId($articleDto->id));
        if ($article === null) {
            throw new ArticleNotFoundException('Article not found');
        }

        $article->updateContent(new Content($articleDto->content));
        $this->articleRepository->update($article);

        $urls = $this->extractorService->extractUrls($articleDto->content);
        $this->articleLinksService->addArticleLinks($urls, $article->id());

        return $article->id()->value();
    }
}
