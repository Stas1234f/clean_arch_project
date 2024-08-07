<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = ['title', 'published_date', 'content'];
}
