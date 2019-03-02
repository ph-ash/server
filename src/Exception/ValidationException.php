<?php

declare(strict_types=1);

namespace App\Exception;

use App\Dto\MonitoringData;
use Exception;
use Throwable;

class ValidationException extends Exception
{
    private $monitoringData;

    public function __construct(
        MonitoringData $monitoringData,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->monitoringData = $monitoringData;
    }

    public function getPath(): string
    {
        return $this->monitoringData->getPath();
    }

    public function getId(): string
    {
        return $this->monitoringData->getId();
    }
}
