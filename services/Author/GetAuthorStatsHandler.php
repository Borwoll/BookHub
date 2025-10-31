<?php

declare(strict_types=1);

namespace app\services\Author;

use app\domain\Author\Repositories\AuthorRepositoryInterface;

final class GetAuthorStatsHandler
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function getStats(int $authorId): array
    {
        $author = $this->authorRepository->findById($authorId);
        if ($author === null) {
            return ['booksCount' => 0, 'subscriptionsCount' => 0];
        }

        $books = $this->authorRepository->getBooksByAuthorWithAuthors($authorId);
        $booksCount = count($books);

        $subscriptionsCount = $this->authorRepository->getActiveSubscriptionsCount($author);

        return [
            'booksCount' => $booksCount,
            'subscriptionsCount' => $subscriptionsCount,
        ];
    }
}
