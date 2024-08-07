<?php

declare(strict_types=1);


use App\Application\Dto\ActualizeLinkDto;
use App\Application\Dto\UpdateArticleLinksDto;
use App\Application\UseCases\CorrectLinkUseCase;
use App\Domain\Enum\LinkStatusEnum;
use App\Domain\Exception\LinkNotFoundException;
use App\Domain\Exception\SameLinkUrlException;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\Service\LinksUpdaterService;
use App\Domain\ValueObjects\LinkId;
use App\Domain\ValueObjects\Status;
use App\Domain\ValueObjects\Url;
use App\Domain\Entities\Link;
use Illuminate\Support\Facades\Http;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CorrectLinkUseCaseUnitTest extends MockeryTestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws SameLinkUrlException
     * @throws LinkNotFoundException
     */
    public function testExecuteWithValidLink(): void
    {
        $linkRepository = $this->createMock(LinkRepositoryInterface::class);
        $linksUpdaterService = $this->createMock(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        $link = $this->createMock(Link::class);
        $link->method('url')->willReturn(new Url('http://old-url.com'));
        $link->method('id')->willReturn(new LinkId(1));
        $link->method('status')->willReturn(new Status(LinkStatusEnum::OK));

        $linkRepository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new LinkId(1)))
            ->willReturn($link);

        Http::shouldReceive('get')
            ->with('http://old-url.com')
            ->andReturn(new class {
                public function status(): int
                {
                    return 404;
                }
            });

        $link->expects($this->once())
            ->method('updateStatusToExpired');

        $linksUpdaterService->expects($this->once())
            ->method('updateArticleLinks')
            ->with($this->isInstanceOf(UpdateArticleLinksDto::class));

        $link->expects($this->once())
            ->method('updateUrl')
            ->with($this->equalTo(new Url('http://new-url.com')));

        $link->expects($this->once())
            ->method('updateDateActualizationToToday');

        $linkRepository->expects($this->once())
            ->method('update')
            ->with($this->equalTo($link));

        $dto = new ActualizeLinkDto(
            id: 1,
            url: 'http://new-url.com'
        );

        $correctLinkUseCase->execute($dto);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws SameLinkUrlException
     */
    public function testExecuteWithLinkNotFound(): void
    {
        $linkRepository = $this->createMock(LinkRepositoryInterface::class);
        $linksUpdaterService = $this->createMock(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        $linkRepository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new LinkId(1)))
            ->willReturn(null);

        $this->expectException(LinkNotFoundException::class);

        $dto = new ActualizeLinkDto(
            id: 1,
            url: 'http://new-url.com'
        );

        $correctLinkUseCase->execute($dto);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws LinkNotFoundException
     */
    public function testExecuteWithSameLinkUrl(): void
    {
        $linkRepository = $this->createMock(LinkRepositoryInterface::class);
        $linksUpdaterService = $this->createMock(LinksUpdaterService::class);

        $correctLinkUseCase = new CorrectLinkUseCase($linkRepository, $linksUpdaterService);

        $link = $this->createMock(Link::class);
        $link->method('url')->willReturn(new Url('http://same-url.com'));

        $linkRepository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new LinkId(1)))
            ->willReturn($link);

        $this->expectException(SameLinkUrlException::class);

        $dto = new ActualizeLinkDto(
            id: 1,
            url: 'http://same-url.com'
        );

        $correctLinkUseCase->execute($dto);
    }
}
