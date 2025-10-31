<?php

declare(strict_types=1);

namespace app\viewModels\Author;

use app\domain\Author\Entities\Author;

final readonly class AuthorViewModel
{
    public function __construct(
        public int $id,
        public string $fullName,
        public int $booksCount = 0,
        public int $activeSubscriptionsCount = 0,
    ) {}

    public static function fromDomainEntity(Author $author, int $booksCount = 0, int $activeSubscriptionsCount = 0): self
    {
        return new self(
            $author->getId(),
            $author->getName()->getValue(),
            $booksCount,
            $activeSubscriptionsCount,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->fullName,
            'books_count' => $this->booksCount,
            'active_subscriptions_count' => $this->activeSubscriptionsCount,
        ];
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'full_name' => $this->fullName,
            'books_count' => $this->booksCount,
            'active_subscriptions_count' => $this->activeSubscriptionsCount,
            default => null,
        };
    }
}
