<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\EventSubscriber\BoardSubscriber;
use App\Exception\PushClientException;
use App\Factory\MonitoringDataDtoFactory;
use App\Service\Board\MonitoringDataPush;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class BoardSubscriberTest extends TestCase
{
    private $monitoringDataPush;
    private $monitoringDataDtoFactory;
    private $logger;
    /** @var BoardSubscriber */
    private $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->monitoringDataPush = $this->prophesize(MonitoringDataPush::class);
        $this->monitoringDataDtoFactory = $this->prophesize(MonitoringDataDtoFactory::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->subject = new BoardSubscriber(
            $this->monitoringDataPush->reveal(),
            $this->monitoringDataDtoFactory->reveal(),
            $this->logger->reveal()
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = BoardSubscriber::getSubscribedEvents();
        self::assertArrayHasKey('monitoring.incoming_data', $subscribedEvents);
        self::assertArrayHasKey('monitoring.grow-tiles', $subscribedEvents);
    }

    public function testPushDataToBoard(): void
    {
        $monitoringData = new MonitoringDataDto('id', 'ok', '', 1, 60, new DateTimeImmutable(), null);
        $incomingEvent = new IncomingMonitoringDataEvent($monitoringData);

        $this->monitoringDataPush->invoke($monitoringData)->shouldBeCalled();

        $this->subject->pushDataToBoard($incomingEvent);
    }

    public function testPushUpdatedDataToBoard(): void
    {
        $monitoringData1 = new MonitoringDataDocument('1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null);
        $monitoringData2 = new MonitoringDataDocument('2', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null);
        $monitorings = [$monitoringData1, $monitoringData2];
        $growEvent = new GrowTilesEvent($monitorings);
        $dto = new MonitoringDataDto('id', 'ok', '', 1, 60, new DateTimeImmutable(), null);

        $this->monitoringDataDtoFactory->createFrom($monitoringData1)->shouldBeCalledOnce()->willReturn($dto);
        $this->monitoringDataDtoFactory->createFrom($monitoringData2)->shouldBeCalledOnce()->willReturn($dto);
        $this->monitoringDataPush->invoke($dto)->shouldBeCalledTimes(2);
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        $this->subject->pushUpdatedDataToBoard($growEvent);
    }

    public function testPushUpdatedDataToBoardException(): void
    {
        $monitoringData1 = new MonitoringDataDocument('1', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null);
        $monitoringData2 = new MonitoringDataDocument('2', 'ok', new DateTimeImmutable(), '', 1, 60, new DateTimeImmutable(), null, null, null);
        $monitorings = [$monitoringData1, $monitoringData2];
        $growEvent = new GrowTilesEvent($monitorings);
        $dto = new MonitoringDataDto('id', 'ok', '', 1, 60, new DateTimeImmutable(), null);

        $this->monitoringDataDtoFactory->createFrom($monitoringData1)->shouldBeCalledOnce()->willReturn($dto);
        $this->monitoringDataDtoFactory->createFrom($monitoringData2)->shouldBeCalledOnce()->willReturn($dto);
        $this->monitoringDataPush->invoke($dto)->shouldBeCalledTimes(2)->willThrow(PushClientException::class);
        $this->logger->error(Argument::cetera())->shouldBeCalledTimes(2);

        $this->subject->pushUpdatedDataToBoard($growEvent);
    }
}
