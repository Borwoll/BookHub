<?php

declare(strict_types=1);

namespace app\commands\Author;

use app\dto\Author\CreateAuthorDTO;

final readonly class CreateAuthorCommand
{
    public function __construct(
        public CreateAuthorDTO $authorData,
    ) {}
}
