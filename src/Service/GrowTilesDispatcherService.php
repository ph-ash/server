<?php

declare(strict_types=1);

namespace App\Service;

use App\Event\GrowTilesEvent;
use App\Factory\GrowTilesEvent as GrowTilesEventFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GrowTilesDispatcherService implements GrowTilesDispatcher
{
    private $eventDispatcher;
    private $growTilesEventFactory;

    public function __construct(EventDispatcherInterface $eventDispatcher, GrowTilesEventFactory $growTilesEventFactory)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->growTilesEventFactory = $growTilesEventFactory;
    }

    public function invoke(): void
    {
        $event = $this->growTilesEventFactory->create();
        $this->eventDispatcher->dispatch(GrowTilesEvent::EVENT_NAME, $event);
    }
}
