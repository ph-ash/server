<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Dto\MonitoringData;
use App\Exception\PushClientException;
use App\Factory\ContextFactory;
use App\Factory\LoopFactory;
use InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;
use ZMQ;
use ZMQSocketException;

class PushClientService implements PushClient
{
    private $serializer;
    private $contextFactory;
    private $loopFactory;

    public function __construct(SerializerInterface $serializer, ContextFactory $contextFactory, LoopFactory $loopFactory)
    {
        $this->serializer = $serializer;
        $this->contextFactory = $contextFactory;
        $this->loopFactory = $loopFactory;
    }

    public function send(MonitoringData $monitoringData): void
    {
        $loop = $this->loopFactory->create();
        $context = $this->contextFactory->create($loop);
        $push = $context->getSocket(ZMQ::SOCKET_PUSH);
        try {
            $push->connect('tcp://127.0.0.1:5555');
            $push->on('error', function ($e) {
                throw new PushClientException('Server responded with an error.', 0, $e);
            });
        } catch (ZMQSocketException $e) {
            throw new PushClientException('Connection to Server failed.', 0, $e);
        } catch (InvalidArgumentException $e) {
            throw new PushClientException('Something went wrong.', 0, $e);
        }

        $push->send($this->serializer->serialize($monitoringData, 'json'));
        $loop->run();
        $push->close();
    }
}
