<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\queries\Book\GetBooksQuery;

final class GetBooksWithAuthorsHandler
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(GetBooksQuery $query): array
    {
        $books = $this->bookRepository->findRecent($query->limit);

        return array_map(function ($book): array {
            $authorIds = $book->getAuthors();
            $authorNames = [];

            if ($authorIds !== []) {
                foreach ($authorIds as $authorId) {
                    $author = $this->authorRepository->findById($authorId);
                    if ($author !== null) {
                        $authorNames[] = $author->getName()->getValue();
                    }
                }
            }

            return [
                'id' => $book->getId(),
                'title' => $book->getTitle()->getValue(),
                'year' => $book->getYear()->getValue(),
                'description' => $book->getDescription(),
                'cover_photo' => $book->getCoverPhoto(),
                'authors' => implode(', ', $authorNames),
            ];
        }, $books);
    }
}
