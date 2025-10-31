<?php

declare(strict_types=1);

namespace app\domain\Book\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class BookId extends ValueObject
{
    public function __construct(
        private readonly int $value,
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Book ID must be positive integer');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(ValueObject $object): bool
    {
        return $object instanceof self && $this->value === $object->value;
    }

    public function toArray(): array
    {
        return ['id' => $this->value];
    }
}
