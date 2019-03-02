<?php

declare(strict_types=1);

namespace App\Factory;

use React\EventLoop\LoopInterface;

interface LoopFactory
{
    public function create(): LoopInterface;
}
