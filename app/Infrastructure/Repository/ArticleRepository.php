<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entities\Article;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Content;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\Title;
use App\Infrastructure\Model\Article as ArticleModel;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function create(Article $article): ArticleId
    {
        $model = ArticleModel::query()->create([
            'title' => $article->title()->value(),
            'content' => $article->content()->value(),
            'published_date' => $article->publishedDate()->value(),
        ]);

        return new ArticleId($model->id);
    }

    public function findById(ArticleId $id): ?Article
    {
        $model = ArticleModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return new Article(
            new ArticleId($model->id),
            new Title($model->title),
            new Date($model->published_date),
            new Content($model->content),
        );
    }

    public function delete(ArticleId $id): void
    {
        ArticleModel::destroy($id->value());
    }

    public function update(Article $article): void
    {
        ArticleModel::query()
            ->where('id', '=', $article->id()->value())
            ->update([
                'title' => $article->title()->value(),
                'content' => $article->content()->value(),
                'published_date' => $article->publishedDate()->value(),
            ]);
    }
}
