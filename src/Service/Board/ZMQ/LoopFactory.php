<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use React\EventLoop\LoopInterface;

interface LoopFactory
{
    public function create(): LoopInterface;
}
