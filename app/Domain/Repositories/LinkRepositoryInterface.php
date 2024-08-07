<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Link;
use App\Domain\ValueObjects\LinkId;
use App\Domain\ValueObjects\Url;

interface LinkRepositoryInterface
{
    public function create(Link $link): LinkId;

    public function update(Link $link): void;

    public function findById(LinkId $id): ?Link;

    public function findByUrl(Url $url): ?Link;

    /**
     * @return Link[]
     */
    public function findAll(): array;
}
