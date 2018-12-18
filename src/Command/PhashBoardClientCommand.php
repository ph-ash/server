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
use Thruway\Logging\Logger;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;
use ZMQ;

class PhashBoardClientCommand extends ContainerAwareCommand
{
    /** @var LoopInterface */
    private $loop;

    /** @var SocketWrapper */
    private $ZMQPullSocket;

    /** @var Client */
    private $thruwayClient;

    /** @var OutputInterface */
    private $output;
    private $serializer;
    private $monitoringDataRepository;
    private $voryxConfig;

    public function __construct(
        SerializerInterface $serializer,
        MonitoringDataRepository $monitoringDataRepository,
        array $voryxConfig
    ) {
        parent::__construct();
        $this->serializer = $serializer;
        $this->monitoringDataRepository = $monitoringDataRepository;
        $this->voryxConfig = $voryxConfig;
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
        $this->startVoryxThruwayClient();
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

        //TODO listen to errors

        Logger::set(new NullLogger());
        $this->ZMQPullSocket->on(
            'message',
            function ($payload) {
                $this->info('ZMQ Received: ' . $payload);
                $this->thruwayClient->emit('monitoringData', [$payload]);
            }
        );
    }

    /**
     * @throws Exception
     */
    private function startVoryxThruwayClient(): void
    {
        $this->thruwayClient = new Client($this->voryxConfig['realm'], $this->loop);
        $this->thruwayClient->addTransportProvider(new PawlTransportProvider($this->voryxConfig['trusted_url']));
        $serializer = $this->serializer;
        $monitoringDataRepository = $this->monitoringDataRepository;

        //TODO handle errors

        $this->thruwayClient->on(
            'open',
            function () {
                $this->info('found connection to websocket router');
                $this->thruwayClient->emit('resendMonitoringData');

                //subscribe to the channel, to get messages from board
                $this->thruwayClient->getSubscriber()->subscribe($this->thruwayClient->getSession(), 'phashcontrol',
                function ($payload) {
                    if ($payload[0] === 'boardAvailable') {
                        $this->info('board is connected');
                        $this->thruwayClient->emit('resendMonitoringData');
                    }
                });
            }
        );

        //event for sending all data to the board
        $this->thruwayClient->on(
            'resendMonitoringData',
            function () use ($serializer, $monitoringDataRepository) {
                $this->info('sending all data to board');
                $monitoringDatasets = $monitoringDataRepository->findAll();
                foreach ($monitoringDatasets as $monitoringData) {
                    $payload = $serializer->serialize($monitoringData, 'json');
                    $this->thruwayClient->emit('monitoringData', [$payload]);
                }
                $this->info('sent all data to board');
                if ($this->thruwayClient->getSession()) {
                    $this->thruwayClient->getSession()->publish('phashcontrol', ['"all data sent"']);
                }
            }
        );

        //send monitoringdata to the board
        $this->thruwayClient->on(
            'monitoringData',
            function ($payload) {
                if ($this->thruwayClient->getSession()) {
                    $this->info('Thruway sending: ' . $payload);
                    $this->thruwayClient->getSession()->publish('phashtopic', [$payload]);
                }
            }
        );

        //los connection to the board
        $this->thruwayClient->on(
            'close',
            function () {
                $this->info('lost connection to router');
                $this->thruwayClient->retryConnection();
            }
        );

        $this->thruwayClient->start(false);
    }

    private function shutDownMZMQServer(): void
    {
        if ($this->ZMQPullSocket) {
            $this->ZMQPullSocket->close();
        }
    }

    private function shutDownThruwayClient(): void
    {
        if ($this->thruwayClient) {
            $this->thruwayClient->getSession()->close();
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
