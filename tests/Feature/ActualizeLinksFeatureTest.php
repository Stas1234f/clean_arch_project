<?php

declare(strict_types=1);


use App\Domain\Enum\LinkStatusEnum;
use Tests\TestCase;
use App\Application\UseCases\ActualizeLinksUseCase;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Infrastructure\Model\Link as LinkModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ActualizeLinksFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testExecuteWithActiveLinks(): void
    {
        $link = LinkModel::create([
            'url' => 'http://example.com',
            'status' => LinkStatusEnum::OK->value,
            'date_actualization' => '2023-06-07',
        ]);

        $linkRepository = app(LinkRepositoryInterface::class);
        $logger = Log::getFacadeRoot();

        $actualizeLinksUseCase = new ActualizeLinksUseCase($linkRepository, $logger);

        Http::fake([
            'http://example.com' => Http::response()
        ]);

        $actualizeLinksUseCase->execute([$link->id]);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'status' => LinkStatusEnum::OK->value,
        ]);
    }

    public function testExecuteWithExpiredLinks(): void
    {
        $link = LinkModel::create([
            'url' => 'http://example.com',
            'status' => LinkStatusEnum::EXPIRED->value,
            'date_actualization' => '2024-07-20',
        ]);

        $linkRepository = app(LinkRepositoryInterface::class);
        $logger = Log::getFacadeRoot();

        $actualizeLinksUseCase = new ActualizeLinksUseCase($linkRepository, $logger);

        Http::fake([
            'http://example.com' => Http::response('', 404)
        ]);

        $actualizeLinksUseCase->execute([$link->id]);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'status' => LinkStatusEnum::EXPIRED->value
        ]);
    }
}
