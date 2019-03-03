<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Dto\MonitoringData;
use App\Exception\ZMQClientException;

interface Client
{
    /**
     * @throws ZMQClientException
     */
    public function send(string $data): void;
}
