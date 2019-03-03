<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\GrowTilesEvent;
use App\Service\GrowTiles\FilterGrowableTiles;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterGrowableTilesSubscriber implements EventSubscriberInterface
{

    private $filterGrowableTiles;

    public function __construct(FilterGrowableTiles $filterGrowableTiles)
    {
        $this->filterGrowableTiles = $filterGrowableTiles;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GrowTilesEvent::EVENT_NAME => ['filterGrowableTiles', 10]
        ];
    }

    public function filterGrowableTiles(GrowTilesEvent $event): void
    {
        $growableMonitorings = $this->filterGrowableTiles->invoke($event->getMonitorings());
        if ($growableMonitorings) {
            $event->setMonitorings($growableMonitorings);
        } else {
            $event->stopPropagation();
        }
    }
}
