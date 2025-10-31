<?php

declare(strict_types=1);

namespace app\domain\User\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class Email extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        if ($value === '') {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Invalid email format');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('Email cannot be longer than 255 characters');
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
        return ['email' => $this->value];
    }
}
