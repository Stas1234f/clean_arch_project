<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';

    protected $fillable = ['url', 'status', 'date_actualization'];
}
