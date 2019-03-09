<?php

declare(strict_types=1);

namespace App\ValueObject;

use MyCLabs\Enum\Enum;

class Channel extends Enum
{
    public const PUSH = 'push';
    public const DELETE = 'delete';
}
