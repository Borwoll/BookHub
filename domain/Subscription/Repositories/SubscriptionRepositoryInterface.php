<?php

declare(strict_types=1);

namespace app\domain\Subscription\Repositories;

use app\domain\Author\Entities\Author;
use app\domain\Common\RepositoryInterface;
use app\domain\Subscription\Entities\Subscription;
use app\domain\Subscription\ValueObjects\PhoneNumber;

interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Subscription;

    public function findByPhoneAndAuthor(PhoneNumber $phone, Author $author): ?Subscription;

    public function findActiveByPhoneAndAuthor(PhoneNumber $phone, Author $author): ?Subscription;

    public function findByPhone(PhoneNumber $phone): array;

    public function findActiveByAuthor(Author $author): array;

    public function countActiveByPhone(PhoneNumber $phone): int;

    public function isSubscribed(PhoneNumber $phone, Author $author): bool;

    public function save(mixed $subscription): bool;

    public function delete(mixed $subscription): bool;

    public function getActiveRecordById(int $id): ?\app\models\Subscription;
}
