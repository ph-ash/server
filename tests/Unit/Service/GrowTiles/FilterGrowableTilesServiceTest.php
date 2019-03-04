<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GrowTiles;

use App\Document\MonitoringData;
use App\Service\GrowTiles\FilterGrowableTilesService;
use App\Service\GrowTiles\GrowableFilter;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class FilterGrowableTilesServiceTest extends TestCase
{
    private $growableFilter;
    private $logger;
    /** @var FilterGrowableTilesService */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->growableFilter = $this->prophesize(GrowableFilter::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->subject = new FilterGrowableTilesService($this->growableFilter->reveal(), $this->logger->reveal());
    }

    public function testInvokeWithoutMonitorings(): void
    {
        $this->growableFilter->isGrowable(Argument::any())->shouldNotBeCalled();
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEmpty($this->subject->invoke([]));
    }

    public function testInvokeWithException(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->growableFilter->isGrowable($monitoring)->shouldBeCalled()->willThrow(Exception::class);
        $this->logger->error(Argument::cetera())->shouldBeCalled();

        self::assertEmpty($this->subject->invoke($monitorings));
    }

    public function testInvokeWithNoChange(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->growableFilter->isGrowable($monitoring)->shouldBeCalled()->willReturn(false);
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEmpty($this->subject->invoke($monitorings));
    }

    public function testInvoke(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->growableFilter->isGrowable($monitoring)->shouldBeCalled()->willReturn(true);
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEquals($monitorings, $this->subject->invoke($monitorings));
    }
}
