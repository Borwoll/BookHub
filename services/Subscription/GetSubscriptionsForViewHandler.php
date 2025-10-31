<?php

declare(strict_types=1);

namespace app\services\Subscription;

use app\domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use app\domain\Subscription\ValueObjects\PhoneNumber;

final class GetSubscriptionsForViewHandler
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
    ) {}

    public function handle(PhoneNumber $phone): array
    {
        $subscriptions = $this->subscriptionRepository->findByPhone($phone);

        $activeRecords = [];
        foreach ($subscriptions as $subscription) {
            $activeRecord = $this->subscriptionRepository->getActiveRecordById($subscription->getId());
            if ($activeRecord !== null) {
                $activeRecords[] = $activeRecord;
            }
        }

        return $activeRecords;
    }
}
