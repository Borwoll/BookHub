<?php

declare(strict_types=1);

namespace app\domain\Common;

abstract class ValueObject
{
    abstract public function equals(ValueObject $object): bool;

    abstract public function toArray(): array;

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}
