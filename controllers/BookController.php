<?php

declare(strict_types=1);

namespace app\controllers;

use app\domain\Book\Exceptions\BookNotFoundException;
use app\queries\Book\GetBookQuery;
use Exception;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

final class BookController extends BaseController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $handler = $this->getHandler(\app\services\Book\GetBooksWithAuthorsHandler::class);
        $query = new \app\queries\Book\GetBooksQuery(20);
        $list = $handler->handle($query);

        return $this->render('index', [
            'books' => $list,
            'message' => 'Каталог книг',
        ]);
    }

    public function actionView(int $id): string
    {
        try {
            $handler = $this->getHandler(\app\services\Book\GetBookViewModelHandler::class);
            $query = new GetBookQuery($id);
            $viewModel = $handler->handle($query);

            return $this->render('view', [
                'model' => $viewModel,
            ]);
        } catch (BookNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            throw new NotFoundHttpException('Book not found');
        }
    }

    public function actionCreate(): Response|string
    {
        $model = new \app\models\Book();

        try {
            $handler = $this->getHandler(\app\services\Author\GetAuthorsActiveRecordsHandler::class);
            $authors = $handler->getAll();
        } catch (Exception $e) {
            $authors = [];
        }

        if (Yii::$app->request->isPost) {
            try {
                $postData = Yii::$app->request->post('Book', []);

                $photoFile = UploadedFile::getInstance($model, 'photo_file');
                $coverPhoto = null;
                if ($photoFile !== null) {
                    $fileName = 'uploads/cover_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $photoFile->name);
                    if ($photoFile->saveAs(Yii::getAlias('@webroot') . '/' . $fileName)) {
                        $coverPhoto = $fileName;
                    }
                }

                $dto = \app\dto\Book\CreateBookDTO::fromArray([
                    'title' => $postData['title'] ?? '',
                    'year' => (int) ($postData['year'] ?? 0),
                    'description' => $postData['description'] ?? null,
                    'isbn' => $postData['isbn'] ?? null,
                    'author_ids' => (array) ($postData['author_ids'] ?? []),
                    'cover_photo' => $coverPhoto,
                ]);

                $command = new \app\commands\Book\CreateBookCommand($dto);
                $handler = $this->getHandler(\app\services\Book\CreateBookHandler::class);
                $book = $handler->handle($command);

                Yii::$app->session->setFlash('success', 'Книга успешно создана!');

                return $this->redirect(['view', 'id' => $book->getId()]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', 'Ошибка при создании книги: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        try {
            $bookRepository = $this->getService(\app\domain\Book\Repositories\BookRepositoryInterface::class);
            $activeRecord = $bookRepository->getActiveRecordById($id);

            if ($activeRecord === null) {
                throw new NotFoundHttpException('Book not found');
            }

            if (Yii::$app->request->isPost) {
                try {
                    $postData = Yii::$app->request->post('Book', []);

                    $photoFile = UploadedFile::getInstance($activeRecord, 'photo_file');
                    if ($photoFile !== null) {
                        $fileName = 'uploads/cover_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $photoFile->name);
                        if ($photoFile->saveAs(Yii::getAlias('@webroot') . '/' . $fileName)) {
                            $postData['cover_photo'] = $fileName;
                        }
                    }

                    $dto = \app\dto\Book\UpdateBookDTO::fromArray(array_merge(['id' => $id], $postData));
                    $command = new \app\commands\Book\UpdateBookCommand($id, $dto);
                    $handler = $this->getHandler(\app\services\Book\UpdateBookHandler::class);
                    $handler->handle($command);

                    Yii::$app->session->setFlash('success', 'Книга успешно обновлена!');

                    return $this->redirect(['view', 'id' => $id]);
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', 'Ошибка при обновлении книги: ' . $e->getMessage());
                }
            }

            try {
                $authorsHandler = $this->getHandler(\app\services\Author\GetAuthorsActiveRecordsHandler::class);
                $authors = $authorsHandler->getAll();
            } catch (Exception $e) {
                $authors = [];
            }

            $selectedAuthors = $activeRecord->getAuthorsIds();

            return $this->render('update', [
                'model' => $activeRecord,
                'authors' => $authors,
                'selectedAuthors' => $selectedAuthors,
            ]);
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            throw new NotFoundHttpException('Book not found');
        }
    }

    public function actionDelete(int $id): Response
    {
        try {
            $handler = $this->getHandler(\app\services\Book\DeleteBookHandler::class);
            $handler->handle($id);

            Yii::$app->session->setFlash('success', 'Книга успешно удалена!');

            return $this->redirect(['index']);
        } catch (Throwable $e) {
            $this->handleDomainException($e);
            Yii::$app->session->setFlash('error', 'Ошибка при удалении книги: ' . $e->getMessage());

            return $this->redirect(['index']);
        }
    }
}
