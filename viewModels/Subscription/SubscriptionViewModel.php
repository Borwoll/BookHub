<?php

declare(strict_types=1);

namespace app\viewModels\Subscription;

use app\domain\Subscription\Entities\Subscription;

final readonly class SubscriptionViewModel
{
    public function __construct(
        public int $id,
        public string $phone,
        public int $authorId,
        public string $authorName,
        public bool $isActive,
    ) {}

    public static function fromDomainEntity(Subscription $subscription, string $authorName = ''): self
    {
        return new self(
            $subscription->getId(),
            $subscription->getPhoneNumber()->getValue(),
            $subscription->getAuthorId(),
            $authorName,
            $subscription->isActive(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'author_id' => $this->authorId,
            'author_name' => $this->authorName,
            'is_active' => $this->isActive,
        ];
    }
}
