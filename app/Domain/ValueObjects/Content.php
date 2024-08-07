<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Content
{
    public function __construct(private string $content)
    {
        $this->validate();
    }

    public function value(): string
    {
        return $this->content;
    }

    private function validate(): void
    {
        (empty($this->content)) ?? throw new InvalidArgumentException('Content is empty');
    }
}
