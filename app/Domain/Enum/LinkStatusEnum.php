<?php

namespace App\Domain\Enum;

enum LinkStatusEnum: string
{
    case OK = 'OK';
    case EXPIRED = 'Expired';
}
