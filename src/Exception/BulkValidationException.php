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
        array $validatonExceptions,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validatonExceptions = $validatonExceptions;
    }

    public function getValidatonExceptions(): array
    {
        return $this->validatonExceptions;
    }
}
