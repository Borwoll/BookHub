<?php

declare(strict_types=1);

namespace app\services\Author;

use app\commands\Author\CreateAuthorCommand;
use app\domain\Author\Entities\Author;
use app\domain\Author\Factories\AuthorFactory;
use app\domain\Author\Repositories\AuthorRepositoryInterface;
use RuntimeException;

final class CreateAuthorHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(CreateAuthorCommand $command): Author
    {
        $authorData = $command->authorData->toArray();
        $author = AuthorFactory::create($authorData);

        if ($this->authorRepository->save($author) === false) {
            throw new RuntimeException('Failed to save author');
        }

        return $author;
    }
}
