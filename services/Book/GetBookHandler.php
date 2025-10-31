<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Book\Entities\Book;
use app\domain\Book\Exceptions\BookNotFoundException;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\queries\Book\GetBookQuery;

final class GetBookHandler
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function handle(GetBookQuery $query): Book
    {
        $book = $this->bookRepository->findById($query->id);

        if ($book === null) {
            throw BookNotFoundException::withId($query->id);
        }

        return $book;
    }
}
