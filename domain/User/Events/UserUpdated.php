<?php

declare(strict_types=1);

namespace app\domain\User\Events;

use app\domain\Common\DomainEvent;

final class UserUpdated extends DomainEvent
{
    public function __construct(
        private readonly int $userId,
        private readonly string $changeType,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'user.updated';
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'change_type' => $this->changeType,
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getChangeType(): string
    {
        return $this->changeType;
    }
}
