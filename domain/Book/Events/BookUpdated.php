<?php

declare(strict_types=1);

namespace app\domain\Book\Events;

use app\domain\Common\DomainEvent;

final class BookUpdated extends DomainEvent
{
    public function __construct(
        private readonly int $bookId,
        private readonly string $changeType,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'book.updated';
    }

    public function getPayload(): array
    {
        return [
            'book_id' => $this->bookId,
            'change_type' => $this->changeType,
        ];
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getChangeType(): string
    {
        return $this->changeType;
    }
}
