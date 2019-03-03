<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Exception\ZMQClientException;
use App\ValueObject\Channel;

interface Client
{
    /**
     * @throws ZMQClientException
     */
    public function send(string $message, Channel $channel): void;
}
