<?php

declare(strict_types=1);


use App\Application\Dto\CreateArticleDto;
use App\Application\Service\UrlExtractorService;
use App\Application\UseCases\AddArticleUseCase;
use App\Domain\Entities\Article;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Service\LinksEmbedderService;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Content;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\Title;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddArticleFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testAddArticle(): void
    {
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $extractorService = $this->createMock(UrlExtractorService::class);
        $articleLinksService = $this->createMock(LinksEmbedderService::class);

        $addArticleUseCase = new AddArticleUseCase(
            $articleRepository,
            $extractorService,
            $articleLinksService
        );

        $articleDto = new CreateArticleDto(
            title: 'Test Title',
            content: 'This is a test content with a link http://example.com',
            date: '2024-08-07',
        );

        $article = new Article(
            id: null,
            title: new Title($articleDto->title),
            publishedDate: new Date($articleDto->date),
            content: new Content($articleDto->content)
        );

        $articleRepository->expects($this->once())
            ->method('create')
            ->with($this->equalTo($article))
            ->willReturn(new ArticleId(1));

        $extractorService->expects($this->once())
            ->method('extractUrls')
            ->with($this->equalTo($articleDto->content))
            ->willReturn(['http://example.com']);

        $articleLinksService->expects($this->once())
            ->method('addArticleLinks')
            ->with($this->equalTo(['http://example.com']), $this->equalTo(new ArticleId(1)));

        $result = $addArticleUseCase->execute($articleDto);

        $this->assertEquals(1, $result);
    }
}
