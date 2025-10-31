<?php

declare(strict_types=1);

namespace app\services\Author;

use app\domain\Author\Entities\Author;
use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\queries\Author\GetAuthorQuery;

final class GetAuthorHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(GetAuthorQuery $query): ?Author
    {
        return $this->authorRepository->findById($query->authorId);
    }
}
