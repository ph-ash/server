<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData;
use App\Event\GrowTilesEvent;
use App\EventSubscriber\DetermineTileGrowthSubscriber;
use App\Service\GrowTiles\DetermineTileGrowth;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DetermineTileGrowthSubscriberTest extends TestCase
{
    private $determineTileGrowth;
    /** @var DetermineTileGrowthSubscriber */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->determineTileGrowth = $this->prophesize(DetermineTileGrowth::class);

        $this->subject = new DetermineTileGrowthSubscriber($this->determineTileGrowth->reveal());
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = DetermineTileGrowthSubscriber::getSubscribedEvents();
        self::assertArrayHasKey('monitoring.grow-tiles', $subscribedEvents);
    }

    public function testDetermineGrowingTiles(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];
        $growEvent = new GrowTilesEvent($monitorings);

        $this->determineTileGrowth->invoke($monitorings)->shouldBeCalled()->willReturn([$monitoring]);

        $this->subject->determineTileGrowth($growEvent);

        self::assertFalse($growEvent->isPropagationStopped());
    }
}
