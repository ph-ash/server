<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Throwable;

class BulkValidationException extends RuntimeException
{
    /** @var ValidatorException[] */
    private $validatorExceptions;

    public function __construct(
        array $validatorExceptions,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validatorExceptions = $validatorExceptions;
    }

    public function getValidatorExceptions(): array
    {
        return $this->validatorExceptions;
    }
}
