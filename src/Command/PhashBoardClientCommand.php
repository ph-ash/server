<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MonitoringDataRepository;
use Exception;
use Psr\Log\NullLogger;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Thruway\ClientSession;
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

    /** @var OutputInterface */
    private $output;

    /** @var SerializerInterface */
    private $serializer;

    /** @var MonitoringDataRepository */
    private $monitoringDataRepository;

    public function __construct(SerializerInterface $serializer, MonitoringDataRepository $monitoringDataRepository)
    {
        parent::__construct();
        $this->serializer = $serializer;
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

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
        $this->output = $output;
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
        $this->ZMQPullSocket->on(
            'message',
            function ($payload) {
                $this->info('ZMQ Received: ' . $payload);
                $this->thruwayConnection->emit('monitoringData', [$payload]);
            }
        );
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
                $this->info('Thruway sending: ' . $payload);
                $this->thruwayConnection->getClient()->getSession()->publish('phashtopic', [$payload]);
            }
        );

        $serializer = $this->serializer;
        $monitoringDataRepository = $this->monitoringDataRepository;

        $this->thruwayConnection->on(
            'open',
            function () use ($serializer, $monitoringDataRepository) {
                $monitoringDatasets = $monitoringDataRepository->findAll();
                foreach ($monitoringDatasets as $monitoringData) {
                    $payload = $serializer->serialize($monitoringData, 'json');
                    $this->thruwayConnection->emit('monitoringData', [$payload]);
                }
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

    private function info(string $msg): void
    {
        $this->output->writeln(sprintf('<info>%s</info>', $msg));
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
