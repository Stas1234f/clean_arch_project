<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\ValueObjects\ArticleId;

class DeleteArticleUseCase
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository
    ) {}

    public function execute(int $id): void
    {
        $this->articleRepository->delete(new ArticleId($id));
    }
}
