<?php

declare(strict_types=1);

namespace app\strategies\Book;

use app\domain\Book\Entities\Book;
use app\domain\Book\Factories\BookFactory;
use app\dto\Book\CreateBookDTO;

final class StandardBookCreationStrategy implements BookCreationStrategyInterface
{
    public function create(CreateBookDTO $dto): Book
    {
        return BookFactory::create($dto->toArray());
    }

    public function supports(CreateBookDTO $dto): bool
    {
        return true; // Поддерживает все случаи как fallback
    }
}
