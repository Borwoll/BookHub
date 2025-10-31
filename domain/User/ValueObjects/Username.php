<?php

declare(strict_types=1);

namespace app\domain\User\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class Username extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidArgumentException('Username cannot be empty');
        }

        if (mb_strlen($trimmed) < 3) {
            throw new InvalidArgumentException('Username must be at least 3 characters');
        }

        if (mb_strlen($trimmed) > 50) {
            throw new InvalidArgumentException('Username cannot be longer than 50 characters');
        }

        if (preg_match('/^[a-zA-Z0-9_-]+$/', $trimmed) !== 1) {
            throw new InvalidArgumentException('Username can only contain letters, numbers, underscores and hyphens');
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
        return ['username' => $this->value];
    }
}
