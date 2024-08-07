<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Article;
use App\Domain\ValueObjects\ArticleId;

interface ArticleRepositoryInterface
{
    public function create(Article $article): ArticleId;

    public function update(Article $article): void;

    public function findById(ArticleId $id): ?Article;

    public function delete(ArticleId $id): void;
}
