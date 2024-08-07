<?php

declare(strict_types=1);


use Tests\TestCase;
use App\Application\UseCases\DeleteArticleUseCase;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Infrastructure\Model\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteArticleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteArticle(): void
    {
        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'This is a test article.',
            'published_date' => now(),
        ]);

        $articleRepository = app(ArticleRepositoryInterface::class);
        $useCase = new DeleteArticleUseCase($articleRepository);

        $useCase->execute($article->id);

        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }
}
