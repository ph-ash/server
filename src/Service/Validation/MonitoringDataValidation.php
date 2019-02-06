<?php

declare(strict_types=1);

namespace App\Service\Validation;

use App\Dto\MonitoringData;
use OutOfBoundsException;
use Symfony\Component\Validator\Exception\ValidatorException;

interface MonitoringDataValidation
{
    /**
     * @throws ValidatorException
     * @throws OutOfBoundsException
     */
    public function invoke(MonitoringData $monitoringData);
}
