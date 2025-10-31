<?php

declare(strict_types=1);

namespace app\domain\Author\Events;

use app\domain\Common\DomainEvent;

final class AuthorUpdated extends DomainEvent
{
    public function __construct(
        private readonly int $authorId,
        private readonly string $changeType,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'author.updated';
    }

    public function getPayload(): array
    {
        return [
            'author_id' => $this->authorId,
            'change_type' => $this->changeType,
        ];
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getChangeType(): string
    {
        return $this->changeType;
    }
}
