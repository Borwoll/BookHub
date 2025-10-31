<?php

declare(strict_types=1);

namespace app\domain\Subscription\Entities;

use app\domain\Common\Entity;
use app\domain\Subscription\Events\SubscriptionCreated;
use app\domain\Subscription\Events\SubscriptionDeactivated;
use app\domain\Subscription\ValueObjects\PhoneNumber;

final class Subscription extends Entity
{
    private array $domainEvents = [];

    public function __construct(
        private PhoneNumber $phoneNumber,
        private int $authorId,
        private bool $isActive = true,
        ?int $id = null,
    ) {
        if ($id) {
            $this->setId($id);
        }
    }

    public static function create(PhoneNumber $phoneNumber, int $authorId): self
    {
        $subscription = new self($phoneNumber, $authorId, true);
        $subscription->addDomainEvent(new SubscriptionCreated($subscription));

        return $subscription;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        if ($this->isActive) {
            $this->isActive = false;
            $this->addDomainEvent(new SubscriptionDeactivated($this));
        }
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'phone' => $this->phoneNumber->getValue(),
            'author_id' => $this->authorId,
            'is_active' => $this->isActive,
        ];
    }

    private function addDomainEvent($event): void
    {
        $this->domainEvents[] = $event;
    }
}
