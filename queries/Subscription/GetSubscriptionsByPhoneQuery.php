<?php

declare(strict_types=1);

namespace app\queries\Subscription;

use app\domain\Subscription\ValueObjects\PhoneNumber;

final readonly class GetSubscriptionsByPhoneQuery
{
    public function __construct(
        public PhoneNumber $phone,
    ) {}
}
