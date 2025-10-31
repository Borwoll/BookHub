<?php

declare(strict_types=1);

namespace app\services\Subscription;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Subscription\Commands\CreateSubscriptionCommand;
use app\domain\Subscription\Entities\Subscription;
use app\domain\Subscription\Factories\SubscriptionFactory;
use app\domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use app\domain\Subscription\ValueObjects\PhoneNumber;
use InvalidArgumentException;
use RuntimeException;

final class CreateSubscriptionHandler
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(CreateSubscriptionCommand $command): Subscription
    {
        $data = $command->subscriptionData;

        $author = $this->authorRepository->findById($data->authorId);
        if ($author === null) {
            throw new InvalidArgumentException('Author not found');
        }

        $phoneNumber = new PhoneNumber($data->phone);
        $existingSubscription = $this->subscriptionRepository->findActiveByPhoneAndAuthor($phoneNumber, $author);

        if ($existingSubscription !== null) {
            throw new InvalidArgumentException('Subscription already exists');
        }

        $subscription = SubscriptionFactory::create($data->toArray());

        if ($this->subscriptionRepository->save($subscription) === false) {
            throw new RuntimeException('Failed to save subscription');
        }

        return $subscription;
    }
}
