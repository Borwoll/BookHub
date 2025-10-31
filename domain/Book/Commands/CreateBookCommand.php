<?php

declare(strict_types=1);

namespace app\commands\Book;

use app\dto\Book\CreateBookDTO;

final readonly class CreateBookCommand
{
    public function __construct(
        public CreateBookDTO $bookData,
    ) {}
}
