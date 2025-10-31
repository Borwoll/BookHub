<?php

declare(strict_types=1);

namespace app\domain\Author\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class AuthorName extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Author name cannot be empty');
        }

        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException('Author name cannot be longer than 255 characters');
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
        return ['full_name' => $this->value];
    }
}
