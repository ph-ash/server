<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Dto\Outgoing\MonitoringData as OutgoingMonitoringDataDto;
use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\EventSubscriber\BoardSubscriber;
use App\Factory\MonitoringDataDtoFactory;
use App\Repository\MonitoringDataRepository;
use App\Service\Board\MonitoringDataDeletion;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use App\Service\GrowTiles\MonitoringDataPush as MonitoringDataDocumentPush;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;

class BoardSubscriberTest extends TestCase
{
    private $monitoringDataRepository;
    private $monitoringDataDtoFactory;
    private $monitoringDataDeletion;
    private $monitoringDataDtoPush;
    private $monitoringDataDocumentPush;
    /** @var BoardSubscriber */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->monitoringDataDtoPush = $this->prophesize(MonitoringDataDtoPush::class);
        $this->monitoringDataDocumentPush = $this->prophesize(MonitoringDataDocumentPush::class);
        $this->monitoringDataDeletion = $this->prophesize(MonitoringDataDeletion::class);
        $this->monitoringDataDtoFactory = $this->prophesize(MonitoringDataDtoFactory::class);
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);

        $this->subject = new BoardSubscriber(
            $this->monitoringDataDtoPush->reveal(),
            $this->monitoringDataDocumentPush->reveal(),
            $this->monitoringDataDeletion->reveal(),
            $this->monitoringDataDtoFactory->reveal(),
            $this->monitoringDataRepository->reveal()
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
        $id = 'id';
        $status = 'ok';
        $payload = '';
        $priority = 1;
        $idletimeOut = 60;
        $date = new DateTimeImmutable();
        $documentManager = $this->prophesize(DocumentManager::class);
        $incomingMonitoringData = new MonitoringDataDto(
            $id,
            $status,
            $payload,
            $priority,
            $idletimeOut,
            $date,
            null
        );
        $incomingEvent = new IncomingMonitoringDataEvent($incomingMonitoringData);
        $outgoingMonitoringData = new OutgoingMonitoringDataDto(
            $id,
            $status,
            $payload,
            $priority,
            $idletimeOut,
            $date,
            null
        );
        $monitoringDataDocument = new MonitoringDataDocument(
            $id,
            $status,
            $date,
            $payload,
            $priority,
            $idletimeOut,
            $date,
            null,
            null,
            null
        );

        $this->monitoringDataRepository->find($id)->willReturn($monitoringDataDocument);

        $this->monitoringDataDtoFactory->createOutgoingFromDocument($monitoringDataDocument)->willReturn(
            $outgoingMonitoringData
        );

        $this->monitoringDataRepository->getDocumentManager()->willReturn($documentManager);
        $this->monitoringDataDtoPush->invoke($outgoingMonitoringData)->shouldBeCalled();

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
