<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData;
use App\Event\GrowTilesEvent;
use App\EventSubscriber\FilterGrowableTilesSubscriber;
use App\Service\GrowTiles\FilterGrowableTiles;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FilterGrowableTilesSubscriberTest extends TestCase
{
    private $filterGrowableTiles;
    /** @var FilterGrowableTilesSubscriber */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->filterGrowableTiles = $this->prophesize(FilterGrowableTiles::class);

        $this->subject = new FilterGrowableTilesSubscriber($this->filterGrowableTiles->reveal());
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = FilterGrowableTilesSubscriber::getSubscribedEvents();
        self::assertArrayHasKey('monitoring.grow-tiles', $subscribedEvents);
    }

    public function testFilterGrowableTiles(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];
        $growEvent = new GrowTilesEvent($monitorings);

        $this->filterGrowableTiles->invoke($monitorings)->shouldBeCalled()->willReturn([$monitoring]);

        $this->subject->filterGrowableTiles($growEvent);

        self::assertFalse($growEvent->isPropagationStopped());
    }
}
