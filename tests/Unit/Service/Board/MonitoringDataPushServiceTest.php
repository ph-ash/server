<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Board;

use App\Dto\MonitoringData;
use App\Service\Board\MonitoringDataPushService;
use App\Service\Board\ZMQ\PushClient;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class MonitoringDataPushServiceTest extends TestCase
{
    private $pushClient;

    /** @var MonitoringDataPushService */
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->pushClient = $this->prophesize(PushClient::class);

        $this->subject = new MonitoringDataPushService($this->pushClient->reveal());
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $monitoringDataDto = new MonitoringData('id', 'status', 'payload', 1, 60, new DateTimeImmutable(), 'some.path');
        $this->pushClient->send($monitoringDataDto)->shouldBeCalledOnce();
        $this->subject->invoke($monitoringDataDto);
    }
}
