<?php

declare(strict_types=1);

namespace app\repositories\Author;

use app\domain\Author\Entities\Author;
use app\domain\Author\Factories\AuthorFactory;
use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Author\ValueObjects\AuthorName;
use app\domain\Common\Entity;
use app\models\Author as AuthorActiveRecord;
use app\repositories\BaseRepository;
use InvalidArgumentException;

final class AuthorRepository extends BaseRepository implements AuthorRepositoryInterface
{
    public function findById(int $id): ?Author
    {
        return $this->doFindById($id);
    }

    public function findByName(AuthorName $name): ?Author
    {
        $activeRecord = AuthorActiveRecord::find()
            ->where(['full_name' => $name->getValue()])
            ->one();

        if ($activeRecord === null) {
            return null;
        }

        return AuthorFactory::fromActiveRecord($activeRecord);
    }

    public function searchByName(string $query): array
    {
        $activeRecords = AuthorActiveRecord::find()
            ->where(['like', 'full_name', $query])
            ->all();

        return array_map(
            fn(AuthorActiveRecord $record) => AuthorFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $activeRecords = AuthorActiveRecord::find()
            ->limit($limit)
            ->offset($offset)
            ->all();

        return array_map(
            fn(AuthorActiveRecord $record) => AuthorFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function getBooksByAuthorWithAuthors(int $authorId): array
    {
        return \app\models\Book::find()
            ->innerJoin('{{%book_author}}', '{{%book_author}}.book_id = {{%book}}.id')
            ->where(['{{%book_author}}.author_id' => $authorId])
            ->with('authors')
            ->all();
    }

    public function getActiveRecordById(int $id): ?AuthorActiveRecord
    {
        return AuthorActiveRecord::findOne($id);
    }

    public function getTopAuthorsByYear(int $year, int $limit = 10): array
    {
        return AuthorActiveRecord::find()
            ->select(['{{%author}}.*', 'COUNT({{%book}}.id) as books_count'])
            ->innerJoin('{{%book_author}}', '{{%book_author}}.author_id = {{%author}}.id')
            ->innerJoin('{{%book}}', '{{%book}}.id = {{%book_author}}.book_id AND {{%book}}.year = :year', [':year' => $year])
            ->groupBy('{{%author}}.id')
            ->orderBy('books_count DESC')
            ->limit($limit)
            ->asArray()
            ->all();
    }

    public function getBooksCountByYear(Author $author, int $year): int
    {
        return (int) \app\models\Book::find()
            ->innerJoin('{{%book_author}}', '{{%book_author}}.book_id = {{%book}}.id')
            ->where([
                '{{%book_author}}.author_id' => $author->getId(),
                '{{%book}}.year' => $year,
            ])
            ->count();
    }

    public function getActiveSubscriptionsCount(Author $author): int
    {
        return (int) \app\models\Subscription::find()
            ->where([
                'author_id' => $author->getId(),
                'is_active' => true,
            ])
            ->count();
    }

    protected function doFindById(int $id): ?Author
    {
        $activeRecord = AuthorActiveRecord::findOne($id);
        if ($activeRecord === null) {
            return null;
        }

        return AuthorFactory::fromActiveRecord($activeRecord);
    }

    protected function doSave(Entity $entity): bool
    {
        if (! ($entity instanceof Author)) {
            throw new InvalidArgumentException('Entity must be an instance of Author');
        }

        $author = $entity;

        if ($author->getId() !== null) {
            return $this->updateExisting($author);
        }

        return $this->createNew($author);
    }

    protected function doDelete(Entity $entity): bool
    {
        if (! ($entity instanceof Author)) {
            throw new InvalidArgumentException('Entity must be an instance of Author');
        }

        $author = $entity;
        $activeRecord = AuthorActiveRecord::findOne($author->getId());

        if ($activeRecord === null) {
            return false;
        }

        return $activeRecord->delete() !== false;
    }

    protected function doExists(int $id): bool
    {
        return AuthorActiveRecord::find()->where(['id' => $id])->exists();
    }

    private function createNew(Author $author): bool
    {
        $activeRecord = new AuthorActiveRecord();
        $activeRecord->full_name = $author->getName()->getValue();

        if ($activeRecord->save() === false) {
            return false;
        }

        $author->setId($activeRecord->id);

        return true;
    }

    private function updateExisting(Author $author): bool
    {
        $activeRecord = AuthorActiveRecord::findOne($author->getId());

        if ($activeRecord === null) {
            return false;
        }

        $activeRecord->full_name = $author->getName()->getValue();

        return $activeRecord->save();
    }
}
