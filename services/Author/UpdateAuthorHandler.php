<?php

declare(strict_types=1);

namespace app\services\Author;

use app\commands\Author\UpdateAuthorCommand;
use app\domain\Author\Entities\Author;
use app\domain\Author\Exceptions\AuthorNotFoundException;
use app\domain\Author\Factories\AuthorFactory;
use app\domain\Author\Repositories\AuthorRepositoryInterface;
use RuntimeException;

final class UpdateAuthorHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(UpdateAuthorCommand $command): Author
    {
        $existingAuthor = $this->authorRepository->findById($command->authorData->id);
        if ($existingAuthor === null) {
            throw new AuthorNotFoundException('Author not found');
        }

        $updateData = $command->authorData->toArray();

        $authorData = [
            'id' => $command->authorData->id,
            'full_name' => $updateData['full_name'] ?? $existingAuthor->getName()->getValue(),
        ];

        $author = AuthorFactory::fromArray($authorData);

        if ($this->authorRepository->save($author) === false) {
            throw new RuntimeException('Failed to update author');
        }

        return $author;
    }
}
