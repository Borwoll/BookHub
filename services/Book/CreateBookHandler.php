<?php

declare(strict_types=1);

namespace app\services\Book;

use app\commands\Book\CreateBookCommand;
use app\domain\Book\Entities\Book;
use app\domain\Book\Events\BookCreated;
use app\domain\Book\Factories\BookFactory;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\domain\Book\Services\BookDomainService;
use RuntimeException;

final class CreateBookHandler
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly BookDomainService $domainService,
        private readonly BookCreatedEventHandler $eventHandler,
    ) {}

    public function handle(CreateBookCommand $command): Book
    {
        $bookData = $command->bookData->toArray();
        $book = BookFactory::create($bookData);

        $this->domainService->validateBookForCreation($book);

        if ($this->bookRepository->save($book) === false) {
            throw new RuntimeException('Failed to save book');
        }

        $events = $book->getDomainEvents();
        foreach ($events as $event) {
            if ($event instanceof BookCreated) {
                $this->eventHandler->handle($event);
            }
        }
        $book->clearDomainEvents();

        return $book;
    }
}
