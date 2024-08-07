<?php

declare(strict_types=1);


use App\Domain\ValueObjects\Url;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use App\Application\UseCases\ActualizeLinksUseCase;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\ValueObjects\LinkId;
use App\Domain\Entities\Link;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

class ActualizeLinksUnitTest extends MockeryTestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testExecuteWithActiveLinks(): void
    {
        $linkRepository = $this->createMock(LinkRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $actualizeLinksUseCase = new ActualizeLinksUseCase($linkRepository, $logger);

        $link = $this->createMock(Link::class);
        $link->method('url')->willReturn(new Url('http://example.com'));

        $linkRepository->expects($this->exactly(1))
            ->method('findById')
            ->with($this->equalTo(new LinkId(1)))
            ->willReturn($link);

        Http::shouldReceive('get')
            ->with('http://example.com')
            ->andReturn(new class {
                public function status(): int
                {
                    return 200;
                }
            });

        $link->expects($this->once())
            ->method('updateStatusToActive');

        $actualizeLinksUseCase->execute([1]);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testExecuteWithExpiredLinks(): void
    {
        $linkRepository = $this->createMock(LinkRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $actualizeLinksUseCase = new ActualizeLinksUseCase($linkRepository, $logger);

        $link = $this->createMock(Link::class);
        $link->method('url')->willReturn(new Url('http://example.com'));

        $linkRepository->expects($this->exactly(1))
            ->method('findById')
            ->with($this->equalTo(new LinkId(1)))
            ->willReturn($link);

        Http::shouldReceive('get')
            ->with('http://example.com')
            ->andReturn(new class {
                public function status(): int
                {
                    return 404;
                }
            });

        $actualizeLinksUseCase->execute([1]);
    }
}
