<?php

declare(strict_types=1);

namespace app\repositories\Subscription;

use app\domain\Author\Entities\Author;
use app\domain\Common\Entity;
use app\domain\Subscription\Entities\Subscription;
use app\domain\Subscription\Factories\SubscriptionFactory;
use app\domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use app\domain\Subscription\ValueObjects\PhoneNumber;
use app\models\Subscription as SubscriptionActiveRecord;
use app\repositories\BaseRepository;
use InvalidArgumentException;

final class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function findById(int $id): ?Subscription
    {
        return $this->doFindById($id);
    }

    public function findByPhoneAndAuthor(PhoneNumber $phone, Author $author): ?Subscription
    {
        $activeRecord = SubscriptionActiveRecord::find()
            ->where([
                'phone' => $phone->getValue(),
                'author_id' => $author->getId(),
            ])
            ->one();

        if ($activeRecord === null) {
            return null;
        }

        return SubscriptionFactory::fromActiveRecord($activeRecord);
    }

    public function findActiveByPhoneAndAuthor(PhoneNumber $phone, Author $author): ?Subscription
    {
        $activeRecord = SubscriptionActiveRecord::find()
            ->where([
                'phone' => $phone->getValue(),
                'author_id' => $author->getId(),
                'is_active' => true,
            ])
            ->one();

        if ($activeRecord === null) {
            return null;
        }

        return SubscriptionFactory::fromActiveRecord($activeRecord);
    }

    public function findByPhone(PhoneNumber $phone): array
    {
        $activeRecords = SubscriptionActiveRecord::find()
            ->where(['phone' => $phone->getValue(), 'is_active' => true])
            ->with('author')
            ->all();

        return array_map(
            fn(SubscriptionActiveRecord $record) => SubscriptionFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function findActiveByAuthor(Author $author): array
    {
        $activeRecords = SubscriptionActiveRecord::find()
            ->where(['author_id' => $author->getId(), 'is_active' => true])
            ->all();

        return array_map(
            fn(SubscriptionActiveRecord $record) => SubscriptionFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function countActiveByPhone(PhoneNumber $phone): int
    {
        return (int) SubscriptionActiveRecord::find()
            ->where(['phone' => $phone->getValue(), 'is_active' => true])
            ->count();
    }

    public function isSubscribed(PhoneNumber $phone, Author $author): bool
    {
        return SubscriptionActiveRecord::find()
            ->where([
                'phone' => $phone->getValue(),
                'author_id' => $author->getId(),
                'is_active' => true,
            ])
            ->exists();
    }

    public function getActiveRecordById(int $id): ?SubscriptionActiveRecord
    {
        return SubscriptionActiveRecord::find()
            ->with('author')
            ->where(['id' => $id])
            ->one();
    }

    protected function doFindById(int $id): ?Subscription
    {
        $activeRecord = SubscriptionActiveRecord::findOne($id);
        if ($activeRecord === null) {
            return null;
        }

        return SubscriptionFactory::fromActiveRecord($activeRecord);
    }

    protected function doSave(Entity $entity): bool
    {
        if (! ($entity instanceof Subscription)) {
            throw new InvalidArgumentException('Entity must be an instance of Subscription');
        }

        $subscription = $entity;

        if ($subscription->getId() !== null) {
            return $this->updateExisting($subscription);
        }

        return $this->createNew($subscription);
    }

    protected function doDelete(Entity $entity): bool
    {
        if (! ($entity instanceof Subscription)) {
            throw new InvalidArgumentException('Entity must be an instance of Subscription');
        }

        $subscription = $entity;
        $activeRecord = SubscriptionActiveRecord::findOne($subscription->getId());

        if ($activeRecord === null) {
            return false;
        }

        return $activeRecord->delete() !== false;
    }

    protected function doExists(int $id): bool
    {
        return SubscriptionActiveRecord::find()->where(['id' => $id])->exists();
    }

    private function createNew(Subscription $subscription): bool
    {
        $activeRecord = new SubscriptionActiveRecord();
        $this->mapToActiveRecord($subscription, $activeRecord);

        if ($activeRecord->save() === false) {
            return false;
        }

        $subscription->setId($activeRecord->id);

        return true;
    }

    private function updateExisting(Subscription $subscription): bool
    {
        $activeRecord = SubscriptionActiveRecord::findOne($subscription->getId());

        if ($activeRecord === null) {
            return false;
        }

        $this->mapToActiveRecord($subscription, $activeRecord);

        return $activeRecord->save();
    }

    private function mapToActiveRecord(Subscription $subscription, SubscriptionActiveRecord $activeRecord): void
    {
        $activeRecord->phone = $subscription->getPhoneNumber()->getValue();
        $activeRecord->author_id = $subscription->getAuthorId();
        $activeRecord->is_active = $subscription->isActive();
    }
}
