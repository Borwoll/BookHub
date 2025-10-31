<?php

declare(strict_types=1);

namespace app\services\Book;

use app\commands\Book\UpdateBookCommand;
use app\domain\Book\Entities\Book;
use app\domain\Book\Exceptions\BookNotFoundException;
use app\domain\Book\Factories\BookFactory;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\domain\Book\Services\BookDomainService;
use RuntimeException;

final class UpdateBookHandler
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly BookDomainService $domainService,
    ) {}

    public function handle(UpdateBookCommand $command): Book
    {
        $existingBook = $this->bookRepository->findById($command->bookId);
        if ($existingBook === null) {
            throw new BookNotFoundException('Book not found');
        }

        $updateData = $command->bookData->toArray();

        $bookData = [
            'id' => $command->bookId,
            'title' => $updateData['title'] ?? $existingBook->getTitle()->getValue(),
            'year' => $updateData['year'] ?? $existingBook->getYear()->getValue(),
            'description' => $updateData['description'] ?? $existingBook->getDescription(),
            'isbn' => $updateData['isbn'] ?? ($existingBook->getIsbn()?->getValue()),
            'cover_photo' => $updateData['cover_photo'] ?? $existingBook->getCoverPhoto(),
            'authors' => $updateData['author_ids'] ?? $existingBook->getAuthors(),
        ];

        $book = BookFactory::fromArray($bookData);

        $this->domainService->validateBookForUpdate($book);

        if ($this->bookRepository->save($book) === false) {
            throw new RuntimeException('Failed to update book');
        }

        return $book;
    }
}
