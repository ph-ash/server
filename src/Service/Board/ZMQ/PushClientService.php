<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Dto\MonitoringData;
use App\Exception\PushClientException;
use InvalidArgumentException;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use Symfony\Component\Serializer\SerializerInterface;
use ZMQ;
use ZMQSocketException;

class PushClientService implements PushClient
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function send(MonitoringData $monitoringData): void
    {
        //TODO test
        //TODO exception handling
        $loop = Factory::create();
        $context = new Context($loop);
        $push = $context->getSocket(ZMQ::SOCKET_PUSH);
        try {
            $push->setSockOpt(ZMQ::SOCKOPT_LINGER, 1);
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
