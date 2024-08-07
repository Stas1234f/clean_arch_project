<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Illuminate\Database\Eloquent\Model;

class ArticleLink extends Model
{
    protected $table = 'article_links';

    protected $fillable = ['article_id', 'link_id'];
}
