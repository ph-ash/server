<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Document\MonitoringData as MonitoringDataDocumentEntity;
use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent;
use App\EventSubscriber\PersistanceSubscriber;
use App\Factory\MonitoringDataDocument;
use App\Repository\MonitoringDataRepository;
use App\Service\Persistance\Priority;
use App\Service\Persistance\StatusChanged;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PersistanceSubscriberTest extends TestCase
{
    private $monitoringDataRepository;
    private $monitoringDataDocument;
    private $statusChanged;
    private $priority;
    /** @var PersistanceSubscriber */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->statusChanged = $this->prophesize(StatusChanged::class);
        $this->priority = $this->prophesize(Priority::class);
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);
        $this->monitoringDataDocument = $this->prophesize(MonitoringDataDocument::class);

        $this->subject = new PersistanceSubscriber(
            $this->monitoringDataRepository->reveal(),
            $this->monitoringDataDocument->reveal(),
            $this->statusChanged->reveal(),
            $this->priority->reveal()
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = PersistanceSubscriber::getSubscribedEvents();
        self::assertArrayHasKey('monitoring.incoming_data', $subscribedEvents);
    }

    public function testPersistMonitoringData(): void
    {
        $monitoringData = new MonitoringData(
            'id',
            'status',
            'payload',
            1,
            60,
            new DateTimeImmutable(),
            'root.branch.leaf',
            5,
            '* 2'
        );

        $event = new IncomingMonitoringDataEvent($monitoringData);
        $statusChanged = new DateTimeImmutable('2019-01-01 00:00:00');
        $priority = 15;

        $this->statusChanged->calculate($monitoringData)->shouldBeCalled()->willReturn($statusChanged);
        $this->priority->calculate($monitoringData)->shouldBeCalled()->willReturn($priority);

        $monitoringDataDocument = $this->prophesize(MonitoringDataDocumentEntity::class);

        $this->monitoringDataDocument->createFrom($monitoringData, $statusChanged)->willReturn(
            $monitoringDataDocument->reveal()
        );

        $this->monitoringDataRepository->save($monitoringDataDocument);

        $this->subject->persistMonitoringData($event);

        self::assertSame($priority, $event->getMonitoringData()->getPriority());
    }
}
