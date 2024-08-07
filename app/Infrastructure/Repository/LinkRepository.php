<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entities\Link;
use App\Domain\Enum\LinkStatusEnum;
use App\Domain\Repositories\LinkRepositoryInterface;
use App\Domain\ValueObjects\Date;
use App\Domain\ValueObjects\LinkId;
use App\Domain\ValueObjects\Status;
use App\Domain\ValueObjects\Url;
use App\Infrastructure\Model\Link as LinkModel;

class LinkRepository implements LinkRepositoryInterface
{
    public function create(Link $link): LinkId
    {
        $model = LinkModel::query()
            ->create([
                'url' => $link->url()->value(),
                'status' => $link->status()->value(),
                'date_actualization' => $link->dateActualization()->value(),
            ]);

        return new LinkId($model->id);
    }

    public function update(Link $link): void
    {
        LinkModel::query()
            ->where('id', $link->id()->value())
            ->update([
                'url' => $link->url()->value(),
                'status' => $link->status()->value(),
                'date_actualization' => $link->dateActualization()->value(),
            ]);
    }

    public function findById(LinkId $id): ?Link
    {
        $model = LinkModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return new Link(
            new LinkId($model->id),
            new Url($model->url),
            new Status(LinkStatusEnum::from($model->status)),
            new Date($model->date_actualization),
        );
    }

    public function findByUrl(Url $url): ?Link
    {
        $model = LinkModel::query()->where('url', $url->value())->first();

        if ($model === null) {
            return null;
        }

        return new Link(
            new LinkId($model->id),
            new Url($model->url),
            new Status(LinkStatusEnum::from($model->status)),
            new Date($model->date_actualization),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array
    {
        $collection = LinkModel::all();

        return $collection
            ->map(static function (LinkModel $model) {
                return new Link(
                    new LinkId($model->id),
                    new Url($model->url),
                    new Status($model->status),
                    new Date($model->date_actualization)
                );
            })->toArray();
    }
}
