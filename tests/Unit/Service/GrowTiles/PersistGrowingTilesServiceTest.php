<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GrowTiles;

use App\Document\MonitoringData;
use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringDataRepository;
use App\Service\GrowTiles\PersistGrowingTilesService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class PersistGrowingTilesServiceTest extends TestCase
{
    private $monitoringDataRepository;
    private $logger;
    /** @var PersistGrowingTilesService */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->subject = new PersistGrowingTilesService($this->monitoringDataRepository->reveal(), $this->logger->reveal());
    }

    public function testInvokeWithoutMonitorings(): void
    {
        $this->monitoringDataRepository->save(Argument::any())->shouldNotBeCalled();
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEmpty($this->subject->invoke([]));
    }

    public function testInvokeWithException(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->monitoringDataRepository->save($monitoring)->shouldBeCalled()->willThrow(PersistenceLayerException::class);
        $this->logger->error(Argument::cetera())->shouldBeCalled();

        self::assertEmpty($this->subject->invoke($monitorings));
    }

    public function testInvoke(): void
    {
        $monitoring = new MonitoringData(
            '1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null
        );
        $monitorings = [$monitoring];

        $this->monitoringDataRepository->save($monitoring)->shouldBeCalled();
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        self::assertEquals($monitorings, $this->subject->invoke($monitorings));
    }
}
