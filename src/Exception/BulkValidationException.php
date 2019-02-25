<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

class BulkValidationException extends RuntimeException
{
    /** @var ValidationException[] */
    private $validatonExceptions;

    public function __construct(
        array $validatorExceptions,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validatonExceptions = $validatorExceptions;
    }

    public function getValidatonExceptions(): array
    {
        return $this->validatonExceptions;
    }
}
