<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Enum\LinkStatusEnum;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Content;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\Status;
use App\Domain\ValueObjects\Title;
use App\Domain\ValueObjects\Url;

class Article
{
    public function __construct(
        private ?ArticleId $id,
        private Title $title,
        private Date $publishedDate,
        private Content $content
    ) {}

    public function id(): ?ArticleId
    {
        return $this->id;
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function publishedDate(): Date
    {
        return $this->publishedDate;
    }

    public function content(): Content
    {
        return $this->content;
    }

    public function updateContent(Content $content): void
    {
        $this->content = $content;
    }

    public function updateContentLink(Url $oldUrl, Url $newUrl, Status $status): void
    {
        $pattern = '/<a\s+href="' . preg_quote($oldUrl->value(), '/') . '">(.*?)<\/a>/i';

        if ($status->value() === LinkStatusEnum::EXPIRED->value) {
            $replacement = '<a href="#" rel="nofollow">ссылка устарела</a>';
        } else {
            $replacement = '<a href="' . $newUrl->value() . '">$1</a>';
        }

        $this->content = new Content(preg_replace($pattern, $replacement, $this->content->value()));
    }
}
