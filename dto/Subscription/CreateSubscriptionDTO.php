<?php

declare(strict_types=1);

namespace app\dto\Subscription;

use InvalidArgumentException;

final readonly class CreateSubscriptionDTO
{
    public function __construct(
        public string $phone,
        public int $authorId,
    ) {}

    public static function fromArray(array $data): self
    {
        $phone = $data['phone'] ?? $data['SubscriptionForm']['phone'] ?? '';
        $authorId = $data['author_id'] ?? $data['authorId'] ?? $data['SubscriptionForm']['author_id'] ?? 0;

        if ($phone === '') {
            throw new InvalidArgumentException('Phone number is required');
        }

        if ($authorId === 0) {
            throw new InvalidArgumentException('Author ID is required');
        }

        return new self(
            (string) $phone,
            (int) $authorId,
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'author_id' => $this->authorId,
        ];
    }
}
