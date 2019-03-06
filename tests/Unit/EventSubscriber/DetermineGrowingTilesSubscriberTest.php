<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData;
use App\Event\GrowTilesEvent;
use App\EventSubscriber\DetermineGrowingTilesSubscriber;
use App\Service\GrowTiles\DetermineGrowingTiles;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DetermineGrowingTilesSubscriberTest extends TestCase
{
    private $determineGrowingTiles;
    /** @var DetermineGrowingTilesSubscriber */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->determineGrowingTiles = $this->prophesize(DetermineGrowingTiles::class);

        $this->subject = new DetermineGrowingTilesSubscriber($this->determineGrowingTiles->reveal());
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = DetermineGrowingTilesSubscriber::getSubscribedEvents();
        self::assertArrayHasKey('monitoring.grow-tiles', $subscribedEvents);
    }

    public function testDetermineGrowingTiles(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];
        $growEvent = new GrowTilesEvent($monitorings);

        $this->determineGrowingTiles->invoke($monitorings)->shouldBeCalled()->willReturn([$monitoring]);

        $this->subject->determineGrowingTiles($growEvent);

        self::assertFalse($growEvent->isPropagationStopped());
    }
}
