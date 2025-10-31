<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Book\Repositories\BookRepositoryInterface;
use app\queries\Book\GetBookQuery;
use app\viewModels\Book\BookViewModel;

final class GetBookViewModelHandler
{
    public function __construct(
        private readonly GetBookHandler $getBookHandler,
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function handle(GetBookQuery $query): BookViewModel
    {
        $book = $this->getBookHandler->handle($query);

        $activeRecord = $this->bookRepository->getActiveRecordById($book->getId());

        $authors = [];
        if ($activeRecord !== null) {
            $authors = $activeRecord->getAuthors()->all();
        }

        return BookViewModel::fromDomainEntity(
            $book,
            $authors,
            $activeRecord?->created_at,
            $activeRecord?->updated_at,
        );
    }
}
