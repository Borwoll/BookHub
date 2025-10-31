<?php

declare(strict_types=1);

namespace app\queries\Book;

final readonly class GetBooksQuery
{
    public function __construct(
        public int $limit = 20,
    ) {}
}
