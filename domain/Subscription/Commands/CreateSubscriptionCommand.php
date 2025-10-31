<?php

declare(strict_types=1);

namespace app\domain\Subscription\Commands;

use app\dto\Subscription\CreateSubscriptionDTO;

final readonly class CreateSubscriptionCommand
{
    public function __construct(
        public CreateSubscriptionDTO $subscriptionData,
    ) {}
}
