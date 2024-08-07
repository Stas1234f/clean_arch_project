<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\ValueObjects\LinkId;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

class ActualizeLinksUseCase
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @param int[] $linksIds
     * @return void
     */
    public function execute(array $linksIds): void
    {
        $expiredLinks = [];

        foreach ($linksIds as $linkId) {
            $link = $this->linkRepository->findById(new LinkId($linkId));
            if ($link === null) {
                continue;
            }

            $response = Http::get($link->url()->value());
            if ($response->status() === 200) {
                $link->updateStatusToActive();
            } else {
                $expiredLinks[] = $link->url()->value();
                $link->updateStatusToExpired();
            }
        }

        if (!empty($expiredLinks)) {
            $this->sendNotification($expiredLinks);
        }
    }

    private function sendNotification(array $expiredLinks): void
    {
        $message = "The following links are expired:\n" . implode("\n", $expiredLinks);
        $this->logger->info($message);
    }
}
