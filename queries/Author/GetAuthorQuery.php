<?php

declare(strict_types=1);

namespace app\queries\Author;

final readonly class GetAuthorQuery
{
    public function __construct(
        public int $authorId,
    ) {}
}
