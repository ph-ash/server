<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class IsLeaf extends Constraint
{
    public $message = 'Path \'{{path}}\' includes a MonitoringData leaf, please change your path';
}
