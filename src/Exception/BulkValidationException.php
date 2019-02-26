<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

class BulkValidationException extends RuntimeException
{
    /** @var ValidationException[] */
    private $validationExceptions;

    public function __construct(
        array $validationExceptions,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validationExceptions = $validationExceptions;
    }

    public function getValidationExceptions(): array
    {
        return $this->validationExceptions;
    }
}
