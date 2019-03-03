<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData;
use App\Event\GrowTilesEvent;
use App\EventSubscriber\PersistGrowingTilesSubscriber;
use App\Service\GrowTiles\PersistGrowingTiles;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PersistGrowingTilesSubscriberTest extends TestCase
{
    private $persistGrowingTiles;
    /** @var PersistGrowingTilesSubscriber */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->persistGrowingTiles = $this->prophesize(PersistGrowingTiles::class);

        $this->subject = new PersistGrowingTilesSubscriber($this->persistGrowingTiles->reveal());
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = PersistGrowingTilesSubscriber::getSubscribedEvents();
        self::assertArrayHasKey('monitoring.grow-tiles', $subscribedEvents);
    }

    public function testPersistGrowingTilesStopPropagation(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];
        $growEvent = new GrowTilesEvent($monitorings);

        $this->persistGrowingTiles->invoke($monitorings)->shouldBeCalled()->willReturn([]);

        $this->subject->persistGrowingTiles($growEvent);

        self::assertTrue($growEvent->isPropagationStopped());
    }

    public function testPersistGrowingTiles(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];
        $growEvent = new GrowTilesEvent($monitorings);

        $this->persistGrowingTiles->invoke($monitorings)->shouldBeCalled()->willReturn([$monitoring]);

        $this->subject->persistGrowingTiles($growEvent);

        self::assertFalse($growEvent->isPropagationStopped());
    }
}
