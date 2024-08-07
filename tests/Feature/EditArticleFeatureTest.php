<?php

declare(strict_types=1);


use App\Application\Dto\EditArticleDto;
use App\Application\Service\UrlExtractorService;
use App\Application\UseCases\EditArticleUseCase;
use App\Domain\Exception\ArticleNotFoundException;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Service\LinksEmbedderService;
use App\Infrastructure\Model\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditArticleFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ArticleNotFoundException
     */
    public function testEditArticle(): void
    {
        $article = Article::create([
            'title' => 'Test Title',
            'published_date' => '2024-08-07',
            'content' => 'Original content'
        ]);

        $articleRepository = app(ArticleRepositoryInterface::class);
        $extractorService = app(UrlExtractorService::class);
        $articleLinksService = app(LinksEmbedderService::class);

        $editArticleUseCase = new EditArticleUseCase(
            $articleRepository,
            $extractorService,
            $articleLinksService
        );

        $articleDto = new EditArticleDto(
            id: $article->id,
            content: 'Updated content with a link <a href="http://example.com">http://example.com</a>'
        );

        $result = $editArticleUseCase->execute($articleDto);

        $this->assertDatabaseHas('articles', [
            'id' => $result,
            'content' => 'Updated content with a link <a href="http://example.com">http://example.com</a>'
        ]);

        $this->assertDatabaseHas('links', [
            'url' => 'http://example.com',
        ]);
    }

    public function testEditArticleNotFound(): void
    {
        $articleRepository = app(ArticleRepositoryInterface::class);
        $extractorService = app(UrlExtractorService::class);
        $articleLinksService = app(LinksEmbedderService::class);

        $editArticleUseCase = new EditArticleUseCase(
            $articleRepository,
            $extractorService,
            $articleLinksService
        );

        $articleDto = new EditArticleDto(
            id: 999, // Несуществующий ID
            content: 'Updated content with a link <a href="http://example.com">http://example.com</a>'
        );

        $this->expectException(ArticleNotFoundException::class);

        $editArticleUseCase->execute($articleDto);
    }
}
