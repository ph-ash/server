<?php

declare(strict_types=1);

namespace App\Command;

use Exception;
use Psr\Log\NullLogger;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thruway\Connection;
use Thruway\Logging\Logger;
use ZMQ;

class PhashBoardClientCommand extends ContainerAwareCommand
{
    /** @var LoopInterface */
    private $loop;

    /** @var SocketWrapper */
    private $ZMQPullSocket;

    /** @var Connection */
    private $thruwayConnection;

    /**
     * @throws Exception
     */
    protected function configure(): void
    {
        $this->setName('phash:board-client:start');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loop = Factory::create();
        $this->startZMQServer();
        $this->startThruwayClient();
        $this->loop->run();
    }

    /**
     * @throws Exception
     */
    private function startZMQServer(): void
    {
        $context = new Context($this->loop);

        $this->ZMQPullSocket = $context->getSocket(ZMQ::SOCKET_PULL);
        $this->ZMQPullSocket->bind('tcp://127.0.0.1:5555');

        //$this->ZMQPullSocket->on('error', function ($e) {
        //    var_dump($e->getMessage());
        //});

        Logger::set(new NullLogger());
        $this->ZMQPullSocket->on('message', function ($payload) {
            echo "Received on ZMQ: $payload\n";
            $this->thruwayConnection->emit('monitoringData', [$payload]);
        });
    }

    /**
     * @throws Exception
     */
    private function startThruwayClient(): void
    {
        //TODO put this into a config
        $thruwayConfiguration['realm'] = 'realm1';
        $thruwayConfiguration['trusted_url'] = 'ws://127.0.0.1:8081';

        Logger::set(new NullLogger());

        $this->thruwayConnection = new Connection(
            [
                'realm' => $thruwayConfiguration['realm'],
                'url' => $thruwayConfiguration['trusted_url'],
            ],
            $this->loop
        );
        $this->thruwayConnection->getClient()->start(false);
        $this->thruwayConnection->on(
            'monitoringData',
            function ($payload) {
                echo "Sending through thruway: $payload\n";
                $this->thruwayConnection->getClient()->getSession()->publish('phashtopic', [$payload]);
            }
        );
    }

    private function shutDownMZMQServer(): void
    {
        if ($this->ZMQPullSocket) {
            $this->ZMQPullSocket->close();
        }
    }

    private function shutDownThruwayClient(): void
    {
        if ($this->thruwayConnection) {
            $this->thruwayConnection->close();
        }
    }

    public function __destruct()
    {
        $this->shutDownMZMQServer();
        $this->shutDownThruwayClient();
        if ($this->loop) {
            $this->loop->stop();
        }
    }
}
