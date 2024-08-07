<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dto\ActualizeLinkDto;
use App\Application\Dto\UpdateArticleLinksDto;
use App\Domain\Exception\LinkNotFoundException;
use App\Domain\Exception\SameLinkUrlException;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\Service\LinksUpdaterService;
use App\Domain\ValueObjects\LinkId;
use App\Domain\ValueObjects\Url;
use Illuminate\Support\Facades\Http;

class CorrectLinkUseCase
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
        private readonly LinksUpdaterService $expiredLinksUpdaterService
    ) {}

    /**
     * @throws LinkNotFoundException
     * @throws SameLinkUrlException
     */
    public function execute(ActualizeLinkDto $dto): void
    {
        $link = $this->linkRepository->findById(new LinkId($dto->id));
        if ($link === null) {
            throw new LinkNotFoundException('Link not found');
        } elseif ($dto->url === $link->url()->value()) {
            throw new SameLinkUrlException('Old and new urls in updating link are same');
        }

        $response = Http::get($link->url()->value());
        if ($response->status() === 200) {
            $link->updateStatusToActive();
        } else {
            $link->updateStatusToExpired();
        }

        $updateArticleLinksDto = new UpdateArticleLinksDto(
            $link->id()->value(),
            $link->url()->value(),
            $dto->url,
            $link->status()->value(),
        );
        $this->expiredLinksUpdaterService->updateArticleLinks($updateArticleLinksDto);
        $link->updateUrl(new Url($dto->url));
        $link->updateDateActualizationToToday();
        $this->linkRepository->update($link);
    }
}
