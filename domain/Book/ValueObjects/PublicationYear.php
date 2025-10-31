<?php

declare(strict_types=1);

namespace app\domain\Book\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class PublicationYear extends ValueObject
{
    public function __construct(
        private readonly int $value,
    ) {
        $currentYear = (int) date('Y');

        if ($value < 1000 || $value > ($currentYear + 10)) {
            throw new InvalidArgumentException('Publication year must be between 1000 and ' . ($currentYear + 10));
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
        return ['year' => $this->value];
    }
}
