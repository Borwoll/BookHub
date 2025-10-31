<?php

declare(strict_types=1);

namespace app\services\Subscription;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use app\queries\Subscription\GetSubscriptionsByPhoneQuery;
use app\viewModels\Subscription\SubscriptionViewModel;

final class GetSubscriptionsByPhoneHandler
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(GetSubscriptionsByPhoneQuery $query): array
    {
        $subscriptions = $this->subscriptionRepository->findByPhone($query->phone);

        $viewModels = [];
        foreach ($subscriptions as $subscription) {
            $author = $this->authorRepository->findById($subscription->getAuthorId());
            $authorName = $author ? $author->getName()->getValue() : 'Неизвестен';
            $viewModels[] = SubscriptionViewModel::fromDomainEntity($subscription, $authorName);
        }

        return $viewModels;
    }
}
