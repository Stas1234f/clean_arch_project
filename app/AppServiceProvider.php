<?php

namespace App;

use App\Domain\Repositories\ArticleLinkRepositoryInterface;
use App\Domain\Repositories\ArticleRepositoryInterface;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Infrastructure\Repository\ArticleLinkRepository;
use App\Infrastructure\Repository\ArticleRepository;
use App\Infrastructure\Repository\LinkRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(LinkRepositoryInterface::class, LinkRepository::class);
        $this->app->bind(ArticleLinkRepositoryInterface::class, ArticleLinkRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
