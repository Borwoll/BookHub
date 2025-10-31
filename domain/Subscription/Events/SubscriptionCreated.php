<?php

declare(strict_types=1);

namespace app\domain\Subscription\Events;

use app\domain\Common\DomainEvent;
use app\domain\Subscription\Entities\Subscription;

final class SubscriptionCreated extends DomainEvent
{
    public function __construct(
        private readonly Subscription $subscription,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'subscription.created';
    }

    public function getPayload(): array
    {
        return [
            'subscription_id' => $this->subscription->getId(),
            'phone' => $this->subscription->getPhoneNumber()->getValue(),
            'author_id' => $this->subscription->getAuthorId(),
        ];
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }
}
