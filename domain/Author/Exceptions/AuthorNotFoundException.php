<?php

declare(strict_types=1);

namespace app\domain\Author\Exceptions;

use app\domain\Common\DomainException;

final class AuthorNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Author with ID {$id} not found");
    }

    public static function withName(string $name): self
    {
        return new self("Author with name {$name} not found");
    }
}
