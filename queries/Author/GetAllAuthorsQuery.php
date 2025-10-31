<?php

declare(strict_types=1);

namespace app\queries\Author;

final readonly class GetAllAuthorsQuery
{
    public function __construct(
        public ?string $searchQuery = null,
        public int $limit = 50,
        public int $offset = 0,
    ) {}
}
