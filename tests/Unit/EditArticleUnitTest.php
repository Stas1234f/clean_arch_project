<?php

declare(strict_types=1);

use App\Application\Dto\EditArticleDto;
use App\Application\Service\UrlExtractorService;
use App\Application\UseCases\EditArticleUseCase;
use App\Domain\Entities\Article;
use App\Domain\Exception\ArticleNotFoundException;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Service\LinksEmbedderService;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Content;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\Title;
use Mockery\Adapter\Phpunit\MockeryTestCase;
class EditArticleUnitTest extends MockeryTestCase
{
    /**
     * @throws ArticleNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testEditArticle(): void
    {
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $extractorService = $this->createMock(UrlExtractorService::class);
        $articleLinksService = $this->createMock(LinksEmbedderService::class);

        $editArticleUseCase = new EditArticleUseCase(
            $articleRepository,
            $extractorService,
            $articleLinksService
        );

        $articleDto = new EditArticleDto(
            id: 1,
            content: 'Updated content with a link <a href="http://example.com">http://example.com</a>'
        );

        $article = new Article(
            id: new ArticleId(1),
            title: new Title('Test Title'),
            publishedDate: new Date('2024-08-07'),
            content: new Content('Original content')
        );

        $articleRepository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new ArticleId(1)))
            ->willReturn($article);

        $articleRepository->expects($this->once())
            ->method('update')
            ->with($this->equalTo($article));

        $extractorService->expects($this->once())
            ->method('extractUrls')
            ->with($this->equalTo($articleDto->content))
            ->willReturn(['http://example.com']);

        $articleLinksService->expects($this->once())
            ->method('addArticleLinks')
            ->with($this->equalTo(['http://example.com']), $this->equalTo(new ArticleId(1)));

        $result = $editArticleUseCase->execute($articleDto);

        $this->assertEquals(1, $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testEditArticleNotFound(): void
    {
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $extractorService = $this->createMock(UrlExtractorService::class);
        $articleLinksService = $this->createMock(LinksEmbedderService::class);

        $editArticleUseCase = new EditArticleUseCase(
            $articleRepository,
            $extractorService,
            $articleLinksService
        );

        $articleDto = new EditArticleDto(
            id: 1,
            content: 'Updated content with a link <a href="http://example.com">http://example.com</a>'
        );

        $articleRepository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new ArticleId(1)))
            ->willReturn(null);

        $this->expectException(ArticleNotFoundException::class);

        $editArticleUseCase->execute($articleDto);
    }
}
