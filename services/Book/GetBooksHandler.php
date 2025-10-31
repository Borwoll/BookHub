<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Book\Repositories\BookRepositoryInterface;
use app\queries\Book\GetBooksQuery;

final class GetBooksHandler
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function handle(GetBooksQuery $query): array
    {
        return $this->bookRepository->findRecent($query->limit);
    }
}
