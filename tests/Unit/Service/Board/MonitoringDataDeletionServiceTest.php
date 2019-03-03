<?php

namespace App\Tests\Unit\Service\Board;

use App\Dto\MonitoringData;
use App\Service\Board\MonitoringDataDeletionService;
use App\Service\Board\ZMQ\Client;
use App\ValueObject\Channel;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class MonitoringDataDeletionServiceTest extends TestCase
{
    private $pushClient;
    private $serializer;

    /** @var MonitoringDataDeletionService */
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->pushClient = $this->prophesize(Client::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);

        $this->subject = new MonitoringDataDeletionService($this->pushClient->reveal(), $this->serializer->reveal());
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $monitoringDataDto = new MonitoringData('id', 'status', 'payload', 1, 60, new DateTimeImmutable(), 'some.path');

        $this->serializer->serialize($monitoringDataDto->getId(), 'json')->willReturn($monitoringDataDto->getId());
        $channel = new Channel('delete');
        $this->pushClient->send($monitoringDataDto->getId(), $channel)->shouldBeCalledOnce();
        $this->subject->invoke($monitoringDataDto);
    }
}
