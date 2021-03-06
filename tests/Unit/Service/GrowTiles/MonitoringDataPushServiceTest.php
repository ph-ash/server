<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GrowTiles;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\Outgoing\MonitoringData as MonitoringDataDto;
use App\Exception\ZMQClientException;
use App\Factory\MonitoringDataDtoFactory;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use App\Service\GrowTiles\MonitoringDataPushService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class MonitoringDataPushServiceTest extends TestCase
{
    private $monitoringDataPush;
    private $monitoringDataDtoFactory;
    private $logger;
    /** @var MonitoringDataPushService */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->monitoringDataPush = $this->prophesize(MonitoringDataDtoPush::class);
        $this->monitoringDataDtoFactory = $this->prophesize(MonitoringDataDtoFactory::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->subject = new MonitoringDataPushService(
            $this->monitoringDataPush->reveal(),
            $this->monitoringDataDtoFactory->reveal(),
            $this->logger->reveal()
        );
    }

    public function testInvoke(): void
    {
        $monitoringData1 = new MonitoringDataDocument(
            '1',
            'ok',
            new DateTimeImmutable(),
            '',
            1,
            60,
            new DateTimeImmutable(),
            null,
            null,
            null
        );
        $monitoringData2 = new MonitoringDataDocument(
            '2',
            'ok',
            new DateTimeImmutable(),
            '',
            1,
            60,
            new DateTimeImmutable(),
            null,
            null,
            null
        );
        $monitorings = [$monitoringData1, $monitoringData2];
        $dto = new MonitoringDataDto('id', 'ok', '', 1, 60, new DateTimeImmutable(), null);

        $this->monitoringDataDtoFactory
            ->createOutgoingFromDocument($monitoringData1)
            ->shouldBeCalledOnce()
            ->willReturn($dto);
        $this->monitoringDataDtoFactory
            ->createOutgoingFromDocument($monitoringData2)
            ->shouldBeCalledOnce()
            ->willReturn($dto);
        $this->monitoringDataPush->invoke($dto)->shouldBeCalledTimes(2);
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        $this->subject->invoke($monitorings);
    }

    public function testInvokeException(): void
    {
        $monitoringData1 = new MonitoringDataDocument(
            '1',
            'ok',
            new DateTimeImmutable(),
            '',
            1,
            60,
            new DateTimeImmutable(),
            null,
            null,
            null
        );
        $monitoringData2 = new MonitoringDataDocument(
            '2',
            'ok',
            new DateTimeImmutable(),
            '',
            1,
            60,
            new DateTimeImmutable(),
            null,
            null,
            null
        );
        $monitorings = [$monitoringData1, $monitoringData2];
        $dto = new MonitoringDataDto('id', 'ok', '', 1, 60, new DateTimeImmutable(), null);

        $this->monitoringDataDtoFactory->createOutgoingFromDocument($monitoringData1)->shouldBeCalledOnce()->willReturn(
            $dto
        );
        $this->monitoringDataDtoFactory->createOutgoingFromDocument($monitoringData2)->shouldBeCalledOnce()->willReturn(
            $dto
        );
        $this->monitoringDataPush->invoke($dto)->shouldBeCalledTimes(2)->willThrow(ZMQClientException::class);
        $this->logger->error(Argument::cetera())->shouldBeCalledTimes(2);

        $this->subject->invoke($monitorings);
    }
}
