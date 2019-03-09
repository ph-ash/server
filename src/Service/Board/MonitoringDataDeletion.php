<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Exception\ZMQClientException;
use UnexpectedValueException;

interface MonitoringDataDeletion
{
    /**
     * @throws ZMQClientException
     * @throws UnexpectedValueException
     */
    public function invoke(string $monitoringDataId);
}
