<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MonitoringDataRepository;
use App\ValueObject\Channel;
use Exception;
use Psr\Log\NullLogger;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Thruway\Logging\Logger;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;
use ZMQ;

class PhashBoardClientCommand extends ContainerAwareCommand
{
    private const THRUWAY_PREFIX = 'THRUWAY WEBSOCKET: ';
    private const ZMQ_PREFIX = 'ZMQ: ';

    /** @var LoopInterface */
    private $loop;

    /** @var SocketWrapper */
    private $ZMQPullSocket;

    /** @var Client */
    private $thruwayClient;

    /** @var ConsoleLogger */
    private $consoleLogger;

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
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->consoleLogger = new ConsoleLogger($output);
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

        $this->ZMQPullSocket = $context->getSocket(ZMQ::SOCKET_SUB);
        $this->ZMQPullSocket->bind('tcp://127.0.0.1:5555');
        $this->ZMQPullSocket->subscribe(Channel::PUSH);
        $this->ZMQPullSocket->subscribe(Channel::DELETE);
        Logger::set(new NullLogger());
        $this->ZMQPullSocket->on(
            'messages',
            function ($payload) {
                $this->consoleLogger->info(self::ZMQ_PREFIX . 'Received {message} on channel {channel}', ['message' => $payload[1], 'channel' => $payload[0]]);
                if ($this->thruwayClient) {
                    switch ($payload[0]) {
                        case Channel::PUSH:
                            $this->thruwayClient->emit('pushMonitoringData', [$payload[1]]);
                            break;
                        case Channel::DELETE:
                            $this->thruwayClient->emit('deleteMonitoringData', [$payload[1]]);
                            break;
                    }
                } else {
                    $this->consoleLogger->critical(self::ZMQ_PREFIX . 'No Websocket Client started');
                }
            }
        );

        $this->ZMQPullSocket->on(
            'error',
            function ($payload) {
                $this->consoleLogger->critical(self::ZMQ_PREFIX . 'error {payload}', ['payload' => $payload]);
                //TODO how to handle errors?
            }
        );

        $this->ZMQPullSocket->on(
            'end',
            function () {
                $this->consoleLogger->info(self::ZMQ_PREFIX . 'pullsocket closed');
                //TODO how to handle end of connection while running?
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

        $this->thruwayClient->on(
            'open',
            function () {
                $this->consoleLogger->info(self::THRUWAY_PREFIX . 'found connection to websocket router');
                $this->thruwayClient->emit('resendMonitoringData');

                //subscribe to the channel, to get messages from board
                $this->thruwayClient->getSubscriber()->subscribe(
                    $this->thruwayClient->getSession(),
                    'phashcontrol',
                    function ($payload) {
                        $firstPayload = \json_decode($payload[0]); // is there a way with SerializerInterface?
                        if ($firstPayload === 'boardAvailable') {
                            $this->consoleLogger->info(self::THRUWAY_PREFIX . 'a new board connected');
                            $this->thruwayClient->emit('resendMonitoringData');
                        } else {
                            $this->consoleLogger->critical(
                                self::THRUWAY_PREFIX . 'unknown payload {payload}',
                                ['payload' => implode(' - ', $payload)]
                            );
                        }
                    }
                );
            }
        );

        //event for sending all data to the board
        $this->thruwayClient->on(
            'resendMonitoringData',
            function () use ($serializer, $monitoringDataRepository) {
                $this->consoleLogger->info(self::THRUWAY_PREFIX . 'publishing all data to board');
                $monitoringDatasets = [];

                try {
                    $monitoringDatasets = $monitoringDataRepository->findAll();
                } catch (Exception $exception) {
                    $this->consoleLogger->critical(
                        self::THRUWAY_PREFIX . 'probably no mongodb connection -> {message}',
                        ['message' => $exception->getMessage()]
                    );
                }

                if ($this->thruwayClient->getSession()) {
                    if (empty($monitoringDatasets)) {
                        $this->consoleLogger->info(self::THRUWAY_PREFIX . 'no data for publishing available');
                    } else {
                        foreach ($monitoringDatasets as $monitoringData) {
                            $payload = $serializer->serialize($monitoringData, 'json');
                            $this->thruwayClient->emit('pushMonitoringData', [$payload]);
                        }
                        $this->consoleLogger->info(self::THRUWAY_PREFIX . 'published all data');
                    }

                    $this->thruwayClient->getSession()->publish('phashcontrol', ['"all data sent"']);
                } else {
                    $this->consoleLogger->critical(self::THRUWAY_PREFIX . 'no connection available, please reconnect');
                }
            }
        );

        //send monitoringdata to the board
        $this->thruwayClient->on(
            'pushMonitoringData',
            function ($payload) {
                if ($this->thruwayClient->getSession()) {
                    $this->consoleLogger->info(self::THRUWAY_PREFIX . 'publishing {payload}', ['payload' => $payload]);
                    $this->thruwayClient->getSession()->publish('phashtopic.push', [$payload]);
                } else {
                    $this->consoleLogger->critical(self::THRUWAY_PREFIX . 'no session available, please reconnect');
                }
            }
        );

        $this->thruwayClient->on(
            'deleteMonitoringData',
            function ($payload) {
                if ($this->thruwayClient->getSession()) {
                    $this->consoleLogger->info(self::THRUWAY_PREFIX . 'deleting {payload}', ['payload' => $payload]);
                    $this->thruwayClient->getSession()->publish('phashtopic.delete', [$payload]);
                } else {
                    $this->consoleLogger->critical(self::THRUWAY_PREFIX . 'no session available, please reconnect');
                }
            }
        );

        //los connection to the board
        $this->thruwayClient->on(
            'close',
            function () {
                $this->consoleLogger->critical(self::THRUWAY_PREFIX . 'lost connection to router, trying to reconnect');
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

    public function __destruct()
    {
        $this->shutDownMZMQServer();
        $this->shutDownThruwayClient();
        if ($this->loop) {
            $this->loop->stop();
        }
    }
}
