<?php

declare(strict_types=1);

use App\Application\Dto\ActualizeLinkDto;
use App\Application\UseCases\CorrectLinkUseCase;
use App\Domain\Enum\LinkStatusEnum;
use App\Domain\Exception\LinkNotFoundException;
use App\Domain\Exception\SameLinkUrlException;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\Service\LinksUpdaterService;
use App\Infrastructure\Model\Article;
use App\Infrastructure\Model\ArticleLink;
use App\Infrastructure\Model\Link as LinkModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CorrectLinkFeatureTest extends TestCase
{
    use RefreshDatabase;
    public function testExecuteWithValidLink(): void
    {
        $link = LinkModel::create([
            'url' => 'http://old-url.com',
            'status' => LinkStatusEnum::OK->value,
            'date_actualization' => date('Y-m-d'),
        ]);

        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'This is a test article with a link <a href="http://old-url.com">Тест</a>',
            'published_date' => date('Y-m-d'),
        ]);

        ArticleLink::create([
            'article_id' => $article->id,
            'link_id' => $link->id,
        ]);

        $linkRepository = app(LinkRepositoryInterface::class);
        $linksUpdaterService = app(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        Http::fake([
            'http://new-url.com' => Http::response(),
        ]);

        $dto = new ActualizeLinkDto(
            id: $link->id,
            url: 'http://new-url.com'
        );

        $correctLinkUseCase->execute($dto);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'url' => 'http://new-url.com',
            'status' => LinkStatusEnum::OK->value,
        ]);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'date_actualization' => date('Y-m-d'),
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'content' => 'This is a test article with a link <a href="http://new-url.com">Тест</a>',
        ]);
    }

    public function testExecuteWithLinkNotFound(): void
    {
        $linkRepository = app(LinkRepositoryInterface::class);
        $linksUpdaterService = app(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        $dto = new ActualizeLinkDto(
            id: 999, // Несуществующий ID
            url: 'http://new-url.com'
        );

        $this->expectException(LinkNotFoundException::class);

        $correctLinkUseCase->execute($dto);
    }

    public function testExecuteWithSameLinkUrl(): void
    {
        $link = LinkModel::create([
            'url' => 'http://same-url.com',
            'status' => LinkStatusEnum::OK->value,
            'date_actualization' => date('Y-m-d'),
        ]);

        $linkRepository = app(LinkRepositoryInterface::class);
        $linksUpdaterService = app(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        $dto = new ActualizeLinkDto(
            id: $link->id,
            url: 'http://same-url.com'
        );

        $this->expectException(SameLinkUrlException::class);

        $correctLinkUseCase->execute($dto);
    }

    public function testUpdateExpiredLinkInArticle(): void
    {
        $link = LinkModel::create([
            'url' => 'http://expired-url.com',
            'status' => LinkStatusEnum::EXPIRED->value,
            'date_actualization' => '2024-06-06',
        ]);

        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'This is a test article with a link <a href="http://expired-url.com">http://expired-url.com</a>',
            'published_date' => '2024-06-07',
        ]);

        ArticleLink::create([
            'article_id' => $article->id,
            'link_id' => $link->id,
        ]);

        $linkRepository = app(LinkRepositoryInterface::class);
        $linksUpdaterService = app(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        Http::fake([
            'http://expired-url.com' => Http::response('', 404),
        ]);

        $dto = new ActualizeLinkDto(
            id: $link->id,
            url: 'http://new-url.com'
        );

        $correctLinkUseCase->execute($dto);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'content' => 'This is a test article with a link <a href="#" rel="nofollow">ссылка устарела</a>',
        ]);
    }
}
