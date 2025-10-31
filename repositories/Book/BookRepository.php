<?php

declare(strict_types=1);

namespace app\repositories\Book;

use app\domain\Book\Entities\Book;
use app\domain\Book\Factories\BookFactory;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\domain\Book\ValueObjects\ISBN;
use app\domain\Common\Entity;
use app\models\Book as BookActiveRecord;
use app\repositories\BaseRepository;
use InvalidArgumentException;

final class BookRepository extends BaseRepository implements BookRepositoryInterface
{
    public function findById(int $id): ?Book
    {
        return $this->doFindById($id);
    }

    public function findByIsbn(ISBN $isbn): ?Book
    {
        $isbnValue = $isbn->getValue();
        if ($isbnValue === '') {
            return null;
        }

        $activeRecord = BookActiveRecord::find()
            ->where(['isbn' => $isbnValue])
            ->with('authors')
            ->one();

        if ($activeRecord === null) {
            return null;
        }

        return BookFactory::fromActiveRecord($activeRecord);
    }

    public function findByTitle(string $title): array
    {
        $activeRecords = BookActiveRecord::find()
            ->where(['like', 'title', $title])
            ->with('authors')
            ->all();

        return array_map(
            fn(BookActiveRecord $record) => BookFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function findByYear(int $year): array
    {
        $activeRecords = BookActiveRecord::find()
            ->where(['year' => $year])
            ->with('authors')
            ->all();

        return array_map(
            fn(BookActiveRecord $record) => BookFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function findRecent(int $limit = 20): array
    {
        $activeRecords = BookActiveRecord::find()
            ->with('authors')
            ->limit($limit)
            ->all();

        return array_map(
            fn(BookActiveRecord $record) => BookFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function getActiveRecordById(int $id): ?BookActiveRecord
    {
        return BookActiveRecord::find()
            ->with('authors')
            ->where(['id' => $id])
            ->one();
    }

    public function findByAuthor(int $authorId): array
    {
        $activeRecords = BookActiveRecord::find()
            ->innerJoin('{{%book_author}}', '{{%book_author}}.book_id = {{%book}}.id')
            ->where(['{{%book_author}}.author_id' => $authorId])
            ->with('authors')
            ->all();

        return array_map(
            fn(BookActiveRecord $record) => BookFactory::fromActiveRecord($record),
            $activeRecords,
        );
    }

    public function getAvailableYears(): array
    {
        return BookActiveRecord::find()
            ->select('year')
            ->distinct()
            ->orderBy('year DESC')
            ->column();
    }

    public function existsByIsbn(ISBN $isbn): bool
    {
        $isbnValue = $isbn->getValue();
        if ($isbnValue === '') {
            return false;
        }

        return BookActiveRecord::find()
            ->where(['isbn' => $isbnValue])
            ->exists();
    }

    protected function doFindById(int $id): ?Book
    {
        $activeRecord = BookActiveRecord::find()
            ->where(['id' => $id])
            ->with('authors')
            ->one();

        if ($activeRecord === null) {
            return null;
        }

        return BookFactory::fromActiveRecord($activeRecord);
    }

    protected function doSave(Entity $entity): bool
    {
        if (! ($entity instanceof Book)) {
            throw new InvalidArgumentException('Entity must be an instance of Book');
        }

        $book = $entity;

        if ($book->getId() !== null) {
            return $this->updateExisting($book);
        }

        return $this->createNew($book);
    }

    protected function doDelete(Entity $entity): bool
    {
        if (! ($entity instanceof Book)) {
            throw new InvalidArgumentException('Entity must be an instance of Book');
        }

        $book = $entity;

        $activeRecord = BookActiveRecord::findOne($book->getId());

        if ($activeRecord === null) {
            return false;
        }

        return $activeRecord->delete() !== false;
    }

    protected function doExists(int $id): bool
    {
        return BookActiveRecord::find()->where(['id' => $id])->exists();
    }

    private function createNew(Book $book): bool
    {
        $activeRecord = new BookActiveRecord();
        $this->mapToActiveRecord($book, $activeRecord);

        if ($activeRecord->save() === false) {
            return false;
        }

        $book->setId($activeRecord->id);
        $this->syncAuthors($book, $activeRecord);

        return true;
    }

    private function updateExisting(Book $book): bool
    {
        $activeRecord = BookActiveRecord::findOne($book->getId());

        if ($activeRecord === null) {
            return false;
        }

        $this->mapToActiveRecord($book, $activeRecord);

        if ($activeRecord->save() === false) {
            return false;
        }

        $this->syncAuthors($book, $activeRecord);

        return true;
    }

    private function mapToActiveRecord(Book $book, BookActiveRecord $activeRecord): void
    {
        $activeRecord->title = $book->getTitle()->getValue();
        $activeRecord->year = $book->getYear()->getValue();
        $activeRecord->description = $book->getDescription();
        $activeRecord->isbn = $book->getIsbn()?->getValue();
        $activeRecord->cover_photo = $book->getCoverPhoto();
    }

    private function syncAuthors(Book $book, BookActiveRecord $activeRecord): void
    {
        \app\models\BookAuthor::deleteAll(['book_id' => $activeRecord->id]);

        foreach ($book->getAuthors() as $authorId) {
            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $activeRecord->id;
            $bookAuthor->author_id = $authorId;
            $bookAuthor->save();
        }
    }
}
