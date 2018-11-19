<?php

declare(strict_types=1);

namespace App\Command;

use Exception;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZMQ;

class ZMQServerTestCommand extends ContainerAwareCommand
{
    /**
     * @throws Exception
     */
    protected function configure()
    {
        $this->setName('phash:zmq-testserver:start');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $context = new Context($loop);

        $pull = $context->getSocket(ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555');

        $pull->on('error', function ($e) {
            var_dump($e->getMessage());
        });

        $pull->on('message', function ($msg) {
            echo "Received: $msg\n";
        });

        $loop->run();
    }


}
