<?php

declare(strict_types=1);

namespace app\domain\Book\Events;

use app\domain\Book\Entities\Book;
use app\domain\Common\DomainEvent;

final class BookCreated extends DomainEvent
{
    public function __construct(
        private readonly Book $book,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'book.created';
    }

    public function getPayload(): array
    {
        return [
            'book_id' => $this->book->getId(),
            'title' => $this->book->getTitle()->getValue(),
            'year' => $this->book->getYear()->getValue(),
            'authors' => $this->book->getAuthors(),
        ];
    }

    public function getBook(): Book
    {
        return $this->book;
    }
}
