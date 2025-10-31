<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Book\Entities\Book;
use app\dto\Book\CreateBookDTO;
use app\strategies\Book\BookCreationStrategyInterface;
use RuntimeException;

final class BookCreationService
{
    private array $strategies;

    public function __construct(BookCreationStrategyInterface ...$strategies)
    {
        $this->strategies = $strategies;
    }

    public function createBook(CreateBookDTO $dto): Book
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($dto)) {
                return $strategy->create($dto);
            }
        }

        throw new RuntimeException('No suitable strategy found for book creation');
    }

    public function addStrategy(BookCreationStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
}
