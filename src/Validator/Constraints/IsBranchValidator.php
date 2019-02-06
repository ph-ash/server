<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringDataRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsBranchValidator extends ConstraintValidator
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
        if (!$constraint instanceof IsBranch) {
            throw new UnexpectedTypeException($constraint, IsBranch::class);
        }

        if ($value !== null) {
            $result = $this->monitoringDataRepository->findLeafs($value);

            if ($result->count(true)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{path}}', $value)
                    ->addViolation();
            }
        }
    }
}
