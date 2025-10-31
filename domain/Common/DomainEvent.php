<?php

declare(strict_types=1);

namespace app\domain\Common;

use DateTimeImmutable;

abstract class DomainEvent implements EventInterface
{
    private DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable();
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    abstract public function getEventName(): string;

    abstract public function getPayload(): array;
}
