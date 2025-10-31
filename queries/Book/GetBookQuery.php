<?php

declare(strict_types=1);

namespace app\queries\Book;

final readonly class GetBookQuery
{
    public function __construct(
        public int $id,
    ) {}
}
