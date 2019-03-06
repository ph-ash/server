<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\EventSubscriber\BoardSubscriber;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use App\Service\GrowTiles\MonitoringDataPush as MonitoringDataDocumentPush;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BoardSubscriberTest extends TestCase
{
    private $monitoringDataDtoPush;
    private $monitoringDataDocumentPush;
    /** @var BoardSubscriber */
    private $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->monitoringDataDtoPush = $this->prophesize(MonitoringDataDtoPush::class);
        $this->monitoringDataDocumentPush = $this->prophesize(MonitoringDataDocumentPush::class);

        $this->subject = new BoardSubscriber($this->monitoringDataDtoPush->reveal(), $this->monitoringDataDocumentPush->reveal());
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

        $this->monitoringDataDtoPush->invoke($monitoringData)->shouldBeCalled();

        $this->subject->pushDataToBoard($incomingEvent);
    }

    public function testPushUpdatedDataToBoard(): void
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
        $growEvent = new GrowTilesEvent($monitorings);

        $this->monitoringDataDocumentPush->invoke($monitorings)->shouldBeCalled();

        $this->subject->pushUpdatedDataToBoard($growEvent);
    }
}
