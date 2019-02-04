<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent as Event;
use App\Factory\IncomingMonitoringDataEvent;
use App\Service\IncomingMonitoringDataDispatcherService;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IncomingMonitoringDataDispatcherServiceTest extends TestCase
{
    private $eventDispatcher;
    private $incomingMonitoringDataEvent;
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp()
    {
        parent::setUp();
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->incomingMonitoringDataEvent = $this->prophesize(IncomingMonitoringDataEvent::class);

        $this->subject = new IncomingMonitoringDataDispatcherService(
            $this->eventDispatcher->reveal(),
            $this->incomingMonitoringDataEvent->reveal()
        );
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $monitoringData = new MonitoringData(
            'someId',
            'someStatus',
            'somePayload',
            5,
            60,
            new DateTimeImmutable()
        );

        $monitoringDataEvent = new Event($monitoringData);

        $this->incomingMonitoringDataEvent->createFrom($monitoringData)->willReturn($monitoringDataEvent)->shouldBeCalledOnce();
        $this->eventDispatcher->dispatch(Event::EVENT_INCOMING_MONITORING_DATA, $monitoringDataEvent)->shouldBeCalledOnce();
        $this->subject->invoke($monitoringData);
    }
}
