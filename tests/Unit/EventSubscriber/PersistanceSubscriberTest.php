<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent;
use App\EventSubscriber\PersistanceSubscriber;
use App\Service\Persistance\PersistMonitoringData;
use App\Service\Persistance\Priority;
use App\Service\Persistance\StatusChanged;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PersistanceSubscriberTest extends TestCase
{
    private $persistMonitoringData;
    private $statusChanged;
    private $priority;
    /** @var PersistanceSubscriber */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->persistMonitoringData = $this->prophesize(PersistMonitoringData::class);
        $this->statusChanged = $this->prophesize(StatusChanged::class);
        $this->priority = $this->prophesize(Priority::class);

        $this->subject = new PersistanceSubscriber(
            $this->persistMonitoringData->reveal(), $this->statusChanged->reveal(), $this->priority->reveal()
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
            'id', 'status', 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $event = new IncomingMonitoringDataEvent($monitoringData);
        $statusChanged = new DateTimeImmutable('2019-01-01 00:00:00');
        $priority = 15;

        $this->statusChanged->calculate($monitoringData)->shouldBeCalled()->willReturn($statusChanged);
        $this->priority->calculate($monitoringData)->shouldBeCalled()->willReturn($priority);

        $this->persistMonitoringData->invoke($monitoringData, $statusChanged)->shouldBeCalled();

        $this->subject->persistMonitoringData($event);

        self::assertSame($priority, $event->getMonitoringData()->getPriority());
    }
}
