<?php

declare(strict_types=1);

namespace app\domain\Common;

use DateTimeImmutable;

interface EventInterface
{
    public function getEventName(): string;

    public function getPayload(): array;

    public function getOccurredAt(): DateTimeImmutable;
}
