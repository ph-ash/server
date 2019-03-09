<?php

declare(strict_types=1);

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
        $this->serializer->serialize('monitoringDataId', 'json')->willReturn('monitoringDataId');
        $channel = new Channel('delete');
        $this->pushClient->send('monitoringDataId', $channel)->shouldBeCalledOnce();
        $this->subject->invoke('monitoringDataId');
    }
}
