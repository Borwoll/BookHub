<?php

declare(strict_types=1);

namespace app\domain\Author\Events;

use app\domain\Author\Entities\Author;
use app\domain\Common\DomainEvent;

final class AuthorCreated extends DomainEvent
{
    public function __construct(
        private readonly Author $author,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'author.created';
    }

    public function getPayload(): array
    {
        return [
            'author_id' => $this->author->getId(),
            'name' => $this->author->getName()->getValue(),
        ];
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }
}
