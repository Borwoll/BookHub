<?php

declare(strict_types=1);

namespace app\domain\Book\Services;

use app\domain\Book\Entities\Book;
use app\domain\Book\Exceptions\BookNotFoundException;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\domain\Book\ValueObjects\ISBN;
use DomainException;

final class BookDomainService
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function isIsbnUnique(ISBN $isbn, ?int $excludeBookId = null): bool
    {
        if ($isbn->getValue() === '') {
            return true;
        }

        $existingBook = $this->bookRepository->findByIsbn($isbn);

        if ($existingBook === null) {
            return true;
        }

        return (bool) ($excludeBookId !== null && $existingBook->getId() === $excludeBookId);
    }

    public function canDeleteBook(Book $book): bool
    {
        return true;
    }

    public function validateBookForCreation(Book $book): void
    {
        if ($book->getIsbn() !== null && $this->isIsbnUnique($book->getIsbn()) === false) {
            throw new DomainException('Book with this ISBN already exists');
        }
    }

    public function validateBookForUpdate(Book $book): void
    {
        if ($book->getId() === null) {
            throw new BookNotFoundException('Cannot update book without ID');
        }

        if ($book->getIsbn() !== null && $this->isIsbnUnique($book->getIsbn(), $book->getId()) === false) {
            throw new DomainException('Book with this ISBN already exists');
        }
    }
}
