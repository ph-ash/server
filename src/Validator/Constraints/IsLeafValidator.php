<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringData as MonitoringDataRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsLeafValidator extends ConstraintValidator
{
    private $monitoringDataRepository;

    public function __construct(MonitoringDataRepository $monitoringDataRepository)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    /**
     * @param mixed $value
     * @throws PersistenceLayerException
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsLeaf) {
            throw new UnexpectedTypeException($constraint, IsLeaf::class);
        }

        if (($value !== null) && $this->monitoringDataRepository->isLeafIncludedInPath($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{path}}', $value)
                ->addViolation();
        }
    }
}
