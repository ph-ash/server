<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GrowTiles;

use App\Document\MonitoringData;
use App\Service\GrowTiles\DetermineTileGrowthService;
use App\Service\GrowTiles\PriorityGrowth;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class DetermineTileGrowthServiceTest extends TestCase
{
    private $priorityGrowth;
    private $logger;
    /** @var DetermineTileGrowthService */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->priorityGrowth = $this->prophesize(PriorityGrowth::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->subject = new DetermineTileGrowthService($this->priorityGrowth->reveal(), $this->logger->reveal());
    }

    public function testInvokeWithoutMonitorings(): void
    {
        $this->priorityGrowth->calculateNewPriority(Argument::any())->shouldNotBeCalled();
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEmpty($this->subject->invoke([]));
    }

    public function testInvokeWithNoChange(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->priorityGrowth->calculateNewPriority($monitoring)->shouldBeCalled()->willReturn(1);
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEmpty($this->subject->invoke($monitorings));
    }

    public function testInvoke(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->priorityGrowth->calculateNewPriority($monitoring)->shouldBeCalled()->willReturn(2);
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEquals($monitorings, $this->subject->invoke($monitorings));
    }
}
