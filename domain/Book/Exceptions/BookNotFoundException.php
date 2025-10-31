<?php

declare(strict_types=1);

namespace app\domain\Book\Exceptions;

use app\domain\Common\DomainException;

final class BookNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Book with ID {$id} not found");
    }

    public static function withIsbn(string $isbn): self
    {
        return new self("Book with ISBN {$isbn} not found");
    }
}
