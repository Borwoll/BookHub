<?php

declare(strict_types=1);

namespace app\services\Author;

use app\domain\Author\Repositories\AuthorRepositoryInterface;

final class GetAuthorsActiveRecordsHandler
{
    public function __construct(
        private readonly GetAllAuthorsHandler $getAllAuthorsHandler,
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function getAll(): array
    {
        $query = new \app\queries\Author\GetAllAuthorsQuery('');
        $authorEntities = $this->getAllAuthorsHandler->handle($query);

        $authors = [];
        foreach ($authorEntities as $author) {
            $activeRecord = $this->authorRepository->getActiveRecordById($author->getId());
            if ($activeRecord !== null) {
                $authors[] = $activeRecord;
            }
        }

        return $authors;
    }
}
