<?php

declare(strict_types=1);

namespace app\services\Author;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\queries\Author\GetAllAuthorsQuery;

final class GetAllAuthorsHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(GetAllAuthorsQuery $query): array
    {
        if ($query->searchQuery !== '') {
            return $this->authorRepository->searchByName($query->searchQuery);
        }

        return $this->authorRepository->findAll($query->limit, $query->offset);
    }
}
