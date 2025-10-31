<?php

declare(strict_types=1);

namespace app\domain\Subscription\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class PhoneNumber extends ValueObject
{
    private const PHONE_PATTERN = '/^\+?[1-9]\d{1,14}$/';

    public function __construct(
        private readonly string $value,
    ) {
        $cleanPhone = $this->cleanPhone($value);

        if ($cleanPhone === '') {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }

        if ($this->isValid($cleanPhone) === false) {
            throw new InvalidArgumentException('Invalid phone number format');
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
        return ['phone' => $this->value];
    }

    private function cleanPhone(string $phone): string
    {
        return trim($phone);
    }

    private function isValid(string $phone): bool
    {
        return (bool) preg_match(self::PHONE_PATTERN, $phone);
    }
}
