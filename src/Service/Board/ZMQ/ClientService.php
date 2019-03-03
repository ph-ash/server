<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Exception\ZMQClientException;
use App\Factory\ContextFactory;
use App\Factory\LoopFactory;
use App\ValueObject\Channel;
use InvalidArgumentException;
use React\EventLoop\LoopInterface;
use React\ZMQ\SocketWrapper;
use ZMQ;
use ZMQSocketException;

final class ClientService implements Client
{
    private $contextFactory;
    private $loopFactory;

    /** @var LoopInterface */
    private $loop;

    /** @var SocketWrapper */
    private $socket;

    public function __construct(ContextFactory $contextFactory, LoopFactory $loopFactory)
    {
        $this->contextFactory = $contextFactory;
        $this->loopFactory = $loopFactory;
    }

    /**
     * @throws ZMQClientException
     */
    private function initSocket(): void
    {
        if ($this->loop === null || $this->socket === null) {
            $this->loop = $this->loopFactory->create();
            $context = $this->contextFactory->create($this->loop);
            $this->socket = $context->getSocket(ZMQ::SOCKET_PUB);
        }

        try {
            $this->socket->connect('tcp://127.0.0.1:5555', true);
            $this->socket->on('error', function ($e) {
                throw new ZMQClientException('Server responded with an error.', 0, $e);
            });
        } catch (ZMQSocketException $e) {
            throw new ZMQClientException('Connection to Server failed.', 0, $e);
        } catch (InvalidArgumentException $e) {
            throw new ZMQClientException('Something went wrong.', 0, $e);
        }
    }

    private function teardown(): void
    {
        $this->loop->run();
        $this->socket->close();
    }

    /**
     * @throws ZMQClientException
     */
    public function send(string $message, Channel $channel): void
    {
        if ($this->loop === null || $this->socket === null) {
            $this->initSocket();
        }

        $this->loop->addPeriodicTimer(1, function ($timer) use ($message, $channel) {
            $this->socket->sendmulti([$channel, $message]);
            $this->loop->cancelTimer($timer);
        });

        $this->teardown();
    }
}
