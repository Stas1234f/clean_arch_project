<?php

declare(strict_types=1);


use Mockery\Adapter\Phpunit\MockeryTestCase;
use App\Application\UseCases\DeleteArticleUseCase;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\ValueObjects\ArticleId;

class DeleteArticleUseCaseUnitTest extends MockeryTestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testDeleteArticle(): void
    {
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(new ArticleId(1)));

        $useCase = new DeleteArticleUseCase($articleRepository);
        $useCase->execute(1);
    }
}
