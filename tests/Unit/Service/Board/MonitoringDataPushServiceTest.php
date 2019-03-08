<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Board;

use App\Dto\MonitoringData;
use App\Service\Board\MonitoringDataPushService;
use App\Service\Board\ZMQ\Client;
use App\ValueObject\Channel;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class MonitoringDataPushServiceTest extends TestCase
{
    private $pushClient;
    private $serializer;

    /** @var MonitoringDataPushService */
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->pushClient = $this->prophesize(Client::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);

        $this->subject = new MonitoringDataPushService($this->pushClient->reveal(), $this->serializer->reveal());
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $monitoringDataDto = new MonitoringData('id', 'status', 'payload', 1, 60, new DateTimeImmutable(), 'some.path', null, null);

        $this->serializer->serialize($monitoringDataDto, 'json')->willReturn('someString');
        $channel = new Channel('push');
        $this->pushClient->send('someString', $channel)->shouldBeCalledOnce();
        $this->subject->invoke($monitoringDataDto);
    }
}
