<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\GrowTilesEvent;
use App\Service\GrowTiles\PersistGrowingTiles;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistGrowingTilesSubscriber implements EventSubscriberInterface
{
    private $persistGrowingTiles;

    public function __construct(PersistGrowingTiles $persistGrowingTiles)
    {
        $this->persistGrowingTiles = $persistGrowingTiles;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GrowTilesEvent::EVENT_NAME => ['persistGrowingTiles', -10]
        ];
    }

    public function persistGrowingTiles(GrowTilesEvent $event): void
    {
        $persistedMonitorings = $this->persistGrowingTiles->invoke($event->getMonitorings());
        $event->setMonitorings($persistedMonitorings);
    }
}
