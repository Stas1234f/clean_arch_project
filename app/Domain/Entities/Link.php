<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Enum\LinkStatusEnum;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\LinkId;
use App\Domain\ValueObjects\Status;
use App\Domain\ValueObjects\Url;

class Link
{
    public function __construct(
        private ?LinkId $linkId,
        private Url $url,
        private Status $status,
        private Date $dateActualiztion
    ) {}

    public function id(): ?LinkId
    {
        return $this->linkId;
    }

    public function url(): Url
    {
        return $this->url;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function dateActualization(): Date
    {
        return $this->dateActualiztion;
    }

    public function updateUrl(Url $url): void
    {
        $this->url = $url;
    }

    public function updateStatusToExpired(): void
    {
        $this->status = new Status(LinkStatusEnum::EXPIRED);
    }

    public function updateStatusToActive(): void
    {
        $this->status = new Status(LinkStatusEnum::OK);
    }

    public function updateDateActualizationToToday(): void
    {
        $this->dateActualiztion = new Date(date('Y-m-d'));
    }
}
