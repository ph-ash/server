<?php

declare(strict_types=1);

namespace App\Service;

use App\Event\GrowTilesEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GrowTilesDispatcherService implements GrowTilesDispatcher
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function invoke(): void
    {
        $this->eventDispatcher->dispatch(GrowTilesEvent::EVENT_NAME);
    }
}
