<?php

declare(strict_types=1);

namespace app\strategies\Book;

use app\domain\Book\Entities\Book;
use app\dto\Book\CreateBookDTO;

interface BookCreationStrategyInterface
{
    public function create(CreateBookDTO $dto): Book;

    public function supports(CreateBookDTO $dto): bool;
}
