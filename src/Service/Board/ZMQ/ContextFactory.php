<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use React\EventLoop\LoopInterface;
use React\ZMQ\Context;

interface ContextFactory
{
    public function create(LoopInterface $loop):  Context;
}
