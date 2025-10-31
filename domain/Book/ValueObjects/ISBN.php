<?php

declare(strict_types=1);

namespace app\domain\Book\ValueObjects;

use app\domain\Common\ValueObject;
use InvalidArgumentException;

final class ISBN extends ValueObject
{
    private const ISBN_PATTERN = '/^(?:ISBN(?:-1[03])?:? )?(?=[0-9X]{10}$|(?=(?:[0-9]+[- ]){3})[- 0-9X]{13}$|97[89][0-9]{10}$|(?=(?:[0-9]+[- ]){4})[- 0-9]{17}$)(?:97[89][- ]?)?[0-9]{1,5}[- ]?[0-9]+[- ]?[0-9]+[- ]?[0-9X]$/';

    public function __construct(
        private readonly string $value,
    ) {
        $cleanIsbn = $this->cleanIsbn($value);

        if ($cleanIsbn !== '' && $this->isValid($cleanIsbn) === false) {
            throw new InvalidArgumentException('Invalid ISBN format');
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
        return ['isbn' => $this->value];
    }

    private function cleanIsbn(string $isbn): string
    {
        return preg_replace('/[^0-9X]/', '', mb_strtoupper(trim($isbn)));
    }

    private function isValid(string $isbn): bool
    {
        if ($isbn === '') {
            return true;
        }

        return (bool) preg_match(self::ISBN_PATTERN, $isbn);
    }
}
