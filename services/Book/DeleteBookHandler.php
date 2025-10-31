<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Book\Exceptions\BookNotFoundException;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\domain\Book\Services\BookDomainService;
use RuntimeException;

final class DeleteBookHandler
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly BookDomainService $domainService,
    ) {}

    public function handle(int $bookId): void
    {
        $book = $this->bookRepository->findById($bookId);
        if ($book === null) {
            throw new BookNotFoundException('Book not found');
        }

        if ($this->domainService->canDeleteBook($book) === false) {
            throw new RuntimeException('Cannot delete this book');
        }

        if ($this->bookRepository->delete($book) === false) {
            throw new RuntimeException('Failed to delete book');
        }
    }
}
