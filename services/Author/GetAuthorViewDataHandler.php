<?php

declare(strict_types=1);

namespace app\services\Author;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\queries\Author\GetAuthorQuery;
use app\viewModels\Author\AuthorViewModel;

final class GetAuthorViewDataHandler
{
    public function __construct(
        private readonly GetAuthorHandler $getAuthorHandler,
        private readonly AuthorRepositoryInterface $authorRepository,
    ) {}

    public function handle(GetAuthorQuery $query): array
    {
        $author = $this->getAuthorHandler->handle($query);

        $activeRecord = $this->authorRepository->getActiveRecordById($author->getId());
        if ($activeRecord === null) {
            throw new \yii\web\NotFoundHttpException('Author not found');
        }

        $books = $this->authorRepository->getBooksByAuthorWithAuthors($author->getId());

        return [
            'activeRecord' => $activeRecord,
            'viewModel' => AuthorViewModel::fromDomainEntity($author),
            'books' => $books,
        ];
    }
}
