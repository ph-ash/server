<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\BulkMonitoringData;
use App\Dto\MonitoringData;
use App\Exception\BulkValidationException;
use App\Exception\ValidationException;
use App\Service\BulkIncomingMonitoringDataDispatcherService;
use App\Service\IncomingMonitoringDataDispatcher;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class BulkIncomingMonitoringDataDispatcherServiceTest extends TestCase
{
    private $incomingMonitoringDataDispatcher;
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp()
    {
        parent::setUp();
        $this->incomingMonitoringDataDispatcher = $this->prophesize(IncomingMonitoringDataDispatcher::class);

        $this->subject = new BulkIncomingMonitoringDataDispatcherService($this->incomingMonitoringDataDispatcher->reveal());
    }

    /**
     * @throws Exception
     */
    public function testInvokeValidationException(): void
    {
        $date = new DateTimeImmutable();
        $monitoringData1 = new MonitoringData(
            'someId1',
            'someStatus',
            'somePayload',
            5,
            60,
            $date,
            'somePath1',
            null,
            null
        );

        $monitoringData2 = new MonitoringData(
            'someId2',
            'someStatus',
            'somePayload',
            5,
            60,
            $date,
            'somePath2',
            null,
            null
        );

        $bulkMonitoringData = new BulkMonitoringData();
        $bulkMonitoringData->setMonitoringData([$monitoringData1, $monitoringData2]);

        $this->incomingMonitoringDataDispatcher->invoke($monitoringData1)->shouldBeCalledOnce();
        $this->incomingMonitoringDataDispatcher->invoke($monitoringData2)->willThrow(ValidationException::class);

        $this->expectExceptionObject(new BulkValidationException([$monitoringData2]));
        $this->subject->invoke($bulkMonitoringData);
    }

    /**
     * @throws Exception
     */
    public function testInvokeValidationSuccess(): void
    {
        $date = new DateTimeImmutable();
        $monitoringData1 = new MonitoringData(
            'someId1',
            'someStatus',
            'somePayload',
            5,
            60,
            $date,
            'somePath1',
            null,
            null
        );

        $monitoringData2 = new MonitoringData(
            'someId2',
            'someStatus',
            'somePayload',
            5,
            60,
            $date,
            'somePath2',
            null,
            null
        );

        $bulkMonitoringData = new BulkMonitoringData();
        $bulkMonitoringData->setMonitoringData([$monitoringData1, $monitoringData2]);

        $this->incomingMonitoringDataDispatcher->invoke($monitoringData1)->shouldBeCalledOnce();
        $this->incomingMonitoringDataDispatcher->invoke($monitoringData2)->shouldBeCalledOnce();

        $this->subject->invoke($bulkMonitoringData);
    }
}
