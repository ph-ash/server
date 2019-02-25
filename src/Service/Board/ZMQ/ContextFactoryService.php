<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use React\EventLoop\LoopInterface;
use React\ZMQ\Context;

class ContextFactoryService implements ContextFactory
{
    public function create(LoopInterface $loop): Context
    {
        return new Context($loop);
    }
}
