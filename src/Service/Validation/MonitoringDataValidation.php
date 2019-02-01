<?php

declare(strict_types=1);

namespace App\Service\Validation;

use App\Dto\MonitoringData;
use Symfony\Component\Validator\Exception\ValidatorException;

interface MonitoringDataValidation
{
    /**
     * @throws ValidatorException
     */
    public function invoke(MonitoringData $monitoringData);
}
