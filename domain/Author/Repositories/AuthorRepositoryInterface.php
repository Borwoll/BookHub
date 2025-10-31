<?php

declare(strict_types=1);

namespace app\domain\Author\Repositories;

use app\domain\Author\Entities\Author;
use app\domain\Author\ValueObjects\AuthorName;
use app\domain\Common\RepositoryInterface;

interface AuthorRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Author;

    public function findByName(AuthorName $name): ?Author;

    public function searchByName(string $query): array;

    public function save(mixed $author): bool;

    public function delete(mixed $author): bool;

    public function getTopAuthorsByYear(int $year, int $limit = 10): array;

    public function getBooksCountByYear(Author $author, int $year): int;

    public function getActiveSubscriptionsCount(Author $author): int;

    public function findAll(int $limit = 50, int $offset = 0): array;

    public function getBooksByAuthorWithAuthors(int $authorId): array;

    public function getActiveRecordById(int $id): ?\app\models\Author;
}
