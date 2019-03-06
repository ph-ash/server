<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Factory\GrowTilesEventFactory;
use App\Repository\MonitoringDataRepository;
use PHPUnit\Framework\TestCase;

class GrowTilesEventFactoryTest extends TestCase
{
    private $monitoringDataRepository;
    /** @var GrowTilesEventFactory */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);

        $this->subject = new GrowTilesEventFactory($this->monitoringDataRepository->reveal());
    }

    public function testCreateEmptyEvent(): void
    {
        $this->monitoringDataRepository->findAllErroneousMonitorings()->willReturn([]);

        $event = $this->subject->create();

        self::assertEmpty($event->getMonitorings());
    }

    public function testCreate(): void
    {
        $monitorings = ['contains monitorings with status error'];
        $this->monitoringDataRepository->findAllErroneousMonitorings()->willReturn($monitorings);

        $event = $this->subject->create();

        self::assertSame($monitorings, $event->getMonitorings());
    }
}
