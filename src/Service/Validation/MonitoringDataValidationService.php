<?php

declare(strict_types=1);

namespace App\Service\Validation;

use App\Dto\MonitoringData;

class MonitoringDataValidationService implements MonitoringDataValidation
{
    /**
     * TODO throw validation exception
     */
    public function invoke(MonitoringData $monitoringData)
    {
        // TODO: Implement invoke() method.
        //TODO use symfony validator
        //TODO add custom validator for path validation
    }
}
