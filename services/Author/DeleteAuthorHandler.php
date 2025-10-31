<?php

declare(strict_types=1);

namespace app\services\Author;

use app\domain\Author\Exceptions\AuthorNotFoundException;
use app\domain\Author\Repositories\AuthorRepositoryInterface;
use RuntimeException;

final class DeleteAuthorHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(int $authorId): void
    {
        $author = $this->authorRepository->findById($authorId);
        if ($author === null) {
            throw new AuthorNotFoundException('Author not found');
        }

        if ($this->authorRepository->delete($author) === false) {
            throw new RuntimeException('Failed to delete author');
        }
    }
}
