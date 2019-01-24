<?php

declare(strict_types=1);

namespace App\Service\Validation;

use App\Dto\MonitoringData;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MonitoringDataValidationService implements MonitoringDataValidation
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function invoke(MonitoringData $monitoringData): void
    {
        $errors = $this->validator->validate($monitoringData);
        if (count($errors) > 0) {
            throw new \Exception('NOOOO');
        }
    }
}
