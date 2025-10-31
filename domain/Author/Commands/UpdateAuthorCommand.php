<?php

declare(strict_types=1);

namespace app\commands\Author;

use app\dto\Author\UpdateAuthorDTO;

final readonly class UpdateAuthorCommand
{
    public function __construct(
        public UpdateAuthorDTO $authorData,
    ) {}
}
