<?php

declare(strict_types=1);

namespace app\dto\Report;

final readonly class TopAuthorsReportDTO
{
    public function __construct(
        public int $authorId,
        public string $authorName,
        public int $booksCount,
        public int $subscriptionsCount = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'author_id' => $this->authorId,
            'author_name' => $this->authorName,
            'books_count' => $this->booksCount,
            'subscriptions_count' => $this->subscriptionsCount,
        ];
    }
}
