<?php

declare(strict_types=1);

namespace app\domain\Book\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class Title extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidArgumentException('Title cannot be empty');
        }

        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException('Title cannot be longer than 255 characters');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $object): bool
    {
        return $object instanceof self && $this->value === $object->value;
    }

    public function toArray(): array
    {
        return ['title' => $this->value];
    }
}
