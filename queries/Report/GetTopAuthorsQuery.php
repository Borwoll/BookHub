<?php

declare(strict_types=1);

namespace app\queries\Report;

final readonly class GetTopAuthorsQuery
{
    public function __construct(
        public int $year,
        public int $limit = 10,
    ) {}
}
