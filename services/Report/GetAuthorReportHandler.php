<?php

declare(strict_types=1);

namespace app\services\Report;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Book\Repositories\BookRepositoryInterface;

final class GetAuthorReportHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function handle(int $authorId, int $year): array
    {
        $author = $this->authorRepository->getActiveRecordById($authorId);
        if ($author === null) {
            return [
                'author' => null,
                'year' => $year,
                'books' => [],
                'booksCount' => 0,
            ];
        }

        $bookEntities = $this->bookRepository->findByAuthor($authorId);
        $books = [];
        foreach ($bookEntities as $bookEntity) {
            $bookActiveRecord = $this->bookRepository->getActiveRecordById($bookEntity->getId());
            if ($bookActiveRecord !== null && $bookActiveRecord->year === $year) {
                $books[] = $bookActiveRecord;
            }
        }

        return [
            'author' => $author,
            'year' => $year,
            'books' => $books,
            'booksCount' => count($books),
        ];
    }
}
