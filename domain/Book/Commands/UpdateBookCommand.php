<?php

declare(strict_types=1);

namespace app\commands\Book;

use app\dto\Book\UpdateBookDTO;

final readonly class UpdateBookCommand
{
    public function __construct(
        public int $bookId,
        public UpdateBookDTO $bookData,
    ) {}
}
