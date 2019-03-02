<?php

declare(strict_types=1);

namespace App\Factory;

use App\Event\GrowTilesEvent as Event;

interface GrowTilesEvent
{
    public function create(): ?Event;
}
