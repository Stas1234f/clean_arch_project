<?php

declare(strict_types=1);


use App\Application\Dto\CreateArticleDto;
use App\Application\Service\UrlExtractorService;
use App\Application\UseCases\AddArticleUseCase;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Service\LinksEmbedderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddArticleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testAddArticle(): void
    {
        $articleRepository = app(ArticleRepositoryInterface::class);
        $extractorService = app(UrlExtractorService::class);
        $articleLinksService = app(LinksEmbedderService::class);

        $addArticleUseCase = new AddArticleUseCase(
            $articleRepository,
            $extractorService,
            $articleLinksService
        );

        $articleDto = new CreateArticleDto(
            title: 'Test Title',
            content: 'This is a test content with a link <a href="http://example.com">Тестовая ссылка</a>',
            date: '2024-08-07'
        );

        $result = $addArticleUseCase->execute($articleDto);

        $this->assertDatabaseHas('articles', [
            'id' => $result,
            'title' => 'Test Title',
            'content' => 'This is a test content with a link <a href="http://example.com">Тестовая ссылка</a>',
            'published_date' => '2024-08-07',
        ]);

        $this->assertDatabaseHas('links', [
            'url' => 'http://example.com',
        ]);
    }
}
