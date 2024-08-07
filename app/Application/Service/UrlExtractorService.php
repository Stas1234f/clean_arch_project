<?php

declare(strict_types=1);

namespace App\Application\Service;

class UrlExtractorService
{
    public function extractUrls(string $text): array
    {
        preg_match_all('/<a href="(.*?)"/', $text, $matches);

        return $matches[1];
    }
}
