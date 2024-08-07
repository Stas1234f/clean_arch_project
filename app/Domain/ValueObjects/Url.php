<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use PharIo\Manifest\InvalidUrlException;

class Url
{
    public function __construct(private string $url)
    {
        $this->validate();
    }

    public function value(): string
    {
        return $this->url;
    }

    private function validate(): void
    {
        if (! filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException;
        }
    }
}
