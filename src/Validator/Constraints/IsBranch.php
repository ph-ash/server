<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class IsBranch extends Constraint
{
    public $message = 'Path \'{{path}}\' is a Branch and cannot contain Monitoringdata';
}
