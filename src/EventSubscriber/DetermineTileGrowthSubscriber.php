<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\GrowTilesEvent;
use App\Service\GrowTiles\DetermineTileGrowth;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DetermineTileGrowthSubscriber implements EventSubscriberInterface
{
    private $determineTileGrowth;

    public function __construct(DetermineTileGrowth $determineTileGrowth)
    {
        $this->determineTileGrowth = $determineTileGrowth;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GrowTilesEvent::EVENT_NAME => ['determineTileGrowth', 0]
        ];
    }

    public function determineTileGrowth(GrowTilesEvent $event): void
    {
        $growingMonitorings = $this->determineTileGrowth->invoke($event->getMonitorings());
        $event->setMonitorings($growingMonitorings);
    }
}
