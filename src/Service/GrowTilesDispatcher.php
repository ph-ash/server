<?php

declare(strict_types=1);

namespace App\Service;

interface GrowTilesDispatcher
{
    public function invoke(): void;
}
