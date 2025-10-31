<?php

declare(strict_types=1);

namespace app\domain\Common;

use Exception;
use Throwable;

abstract class DomainException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
