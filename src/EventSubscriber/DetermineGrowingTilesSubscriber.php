<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\GrowTilesEvent;
use App\Service\GrowTiles\DetermineGrowingTiles;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DetermineGrowingTilesSubscriber implements EventSubscriberInterface
{
    private $determineGrowingTiles;

    public function __construct(DetermineGrowingTiles $determineGrowingTiles)
    {
        $this->determineGrowingTiles = $determineGrowingTiles;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GrowTilesEvent::EVENT_NAME => ['determineGrowingTiles', 0]
        ];
    }

    public function determineGrowingTiles(GrowTilesEvent $event): void
    {
        $growingMonitorings = $this->determineGrowingTiles->invoke($event->getMonitorings());
        $event->setMonitorings($growingMonitorings);
    }
}
