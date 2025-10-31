<?php

declare(strict_types=1);

namespace app\domain\Subscription\Factories;

use app\domain\Subscription\Entities\Subscription;
use app\domain\Subscription\ValueObjects\PhoneNumber;

final class SubscriptionFactory
{
    public static function create(array $data): Subscription
    {
        $phoneNumber = new PhoneNumber($data['phone']);
        $authorId = (int) $data['author_id'];

        return Subscription::create($phoneNumber, $authorId);
    }

    public static function fromArray(array $data): Subscription
    {
        $phoneNumber = new PhoneNumber($data['phone']);
        $authorId = (int) $data['author_id'];
        $isActive = (bool) ($data['is_active'] ?? true);
        $id = isset($data['id']) ? (int) $data['id'] : null;

        return new Subscription($phoneNumber, $authorId, $isActive, $id);
    }

    public static function fromActiveRecord(\app\models\Subscription $activeRecord): Subscription
    {
        $phoneNumber = new PhoneNumber($activeRecord->phone);
        $authorId = (int) $activeRecord->author_id;
        $isActive = (bool) $activeRecord->is_active;
        $id = (int) $activeRecord->id;

        return new Subscription($phoneNumber, $authorId, $isActive, $id);
    }
}
