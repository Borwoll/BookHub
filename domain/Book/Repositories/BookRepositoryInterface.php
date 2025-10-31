<?php

declare(strict_types=1);

namespace app\domain\Book\Repositories;

use app\domain\Book\Entities\Book;
use app\domain\Book\ValueObjects\ISBN;
use app\domain\Common\RepositoryInterface;

interface BookRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Book;

    public function findByIsbn(ISBN $isbn): ?Book;

    public function findByTitle(string $title): array;

    public function findByYear(int $year): array;

    public function findByAuthor(int $authorId): array;

    public function getAvailableYears(): array;

    public function save(mixed $book): bool;

    public function delete(mixed $book): bool;

    public function existsByIsbn(ISBN $isbn): bool;

    public function findRecent(int $limit = 20): array;

    public function getActiveRecordById(int $id): ?\app\models\Book;
}
