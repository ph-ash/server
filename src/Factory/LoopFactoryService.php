<?php

declare(strict_types=1);

namespace App\Factory;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class LoopFactoryService implements LoopFactory
{
    public function create(): LoopInterface
    {
        // @codeCoverageIgnoreStart
        //cannot test static code... sucks!
        return Factory::create();
        // @codeCoverageIgnoreEnd
    }
}
