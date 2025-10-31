<?php

declare(strict_types=1);

namespace app\controllers;

use app\commands\Author\CreateAuthorCommand;
use app\dto\Author\CreateAuthorDTO;
use app\queries\Author\GetAllAuthorsQuery;
use app\queries\Author\GetAuthorQuery;
use app\services\Author\CreateAuthorHandler;
use app\services\Author\DeleteAuthorHandler;
use app\services\Author\GetAllAuthorsHandler;
use app\services\Author\GetAuthorHandler;
use app\services\Author\GetAuthorStatsHandler;
use app\services\Author\UpdateAuthorHandler;
use app\viewModels\Author\AuthorViewModel;
use Exception;
use InvalidArgumentException;
use Throwable;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class AuthorController extends BaseController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'subscribe'],
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
        $searchQuery = Yii::$app->request->get('search', '');

        try {
            $handler = $this->getHandler(GetAllAuthorsHandler::class);
            $query = new GetAllAuthorsQuery($searchQuery);
            $authors = $handler->handle($query);

            $statsHandler = $this->getHandler(GetAuthorStatsHandler::class);
            $viewModels = [];
            foreach ($authors as $author) {
                $stats = $statsHandler->getStats($author->getId());
                $viewModels[] = AuthorViewModel::fromDomainEntity($author, $stats['booksCount'], $stats['subscriptionsCount']);
            }

            return $this->render('index', [
                'authors' => $viewModels,
                'searchQuery' => $searchQuery,
            ]);
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            return $this->render('index', [
                'authors' => [],
                'searchQuery' => $searchQuery,
                'error' => 'Ошибка при загрузке авторов',
            ]);
        }
    }

    public function actionView(int $id): string
    {
        try {
            $handler = $this->getHandler(\app\services\Author\GetAuthorViewDataHandler::class);
            $query = new GetAuthorQuery($id);
            $data = $handler->handle($query);

            $booksDataProvider = new ArrayDataProvider([
                'allModels' => $data['books'],
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['title', 'year'],
                ],
            ]);

            return $this->render('view', [
                'model' => $data['activeRecord'],
                'viewModel' => $data['viewModel'],
                'booksDataProvider' => $booksDataProvider,
            ]);
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            throw new NotFoundHttpException('Author not found');
        }
    }

    public function actionCreate(): Response|string
    {
        if (Yii::$app->request->isPost) {
            try {
                $handler = $this->getHandler(CreateAuthorHandler::class);

                $postData = Yii::$app->request->post();
                $dto = CreateAuthorDTO::fromArray($postData);
                $command = new CreateAuthorCommand($dto);

                $author = $handler->handle($command);

                Yii::$app->session->setFlash('success', 'Автор успешно создан!');

                return $this->redirect(['view', 'id' => $author->getId()]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', 'Ошибка при создании автора: ' . $e->getMessage());
            }
        }

        $model = new \app\models\Author();

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        try {
            $authorRepository = $this->getService(\app\domain\Author\Repositories\AuthorRepositoryInterface::class);
            $activeRecord = $authorRepository->getActiveRecordById($id);

            if ($activeRecord === null) {
                throw new NotFoundHttpException('Author not found');
            }

            if (Yii::$app->request->isPost) {
                try {
                    $postData = Yii::$app->request->post();
                    $dto = \app\dto\Author\UpdateAuthorDTO::fromArray(array_merge(['id' => $id], $postData));
                    $command = new \app\commands\Author\UpdateAuthorCommand($dto);
                    $handler = $this->getHandler(UpdateAuthorHandler::class);
                    $handler->handle($command);

                    Yii::$app->session->setFlash('success', 'Автор успешно обновлен!');

                    return $this->redirect(['view', 'id' => $id]);
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', 'Ошибка при обновлении автора: ' . $e->getMessage());
                }
            }

            return $this->render('update', [
                'model' => $activeRecord,
            ]);
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            throw new NotFoundHttpException('Author not found');
        }
    }

    public function actionDelete(int $id): Response
    {
        try {
            $handler = $this->getHandler(DeleteAuthorHandler::class);
            $handler->handle($id);

            Yii::$app->session->setFlash('success', 'Автор успешно удален!');

            return $this->redirect(['index']);
        } catch (Throwable $e) {
            $this->handleDomainException($e);
            Yii::$app->session->setFlash('error', 'Ошибка при удалении автора: ' . $e->getMessage());

            return $this->redirect(['index']);
        }
    }

    public function actionSubscribe(int $id): Response|string
    {
        try {
            $getAuthorHandler = $this->getHandler(GetAuthorHandler::class);
            $query = new GetAuthorQuery($id);
            $author = $getAuthorHandler->handle($query);

            if ($author === null) {
                throw new NotFoundHttpException('Author not found');
            }

            if (Yii::$app->request->isPost) {
                $phone = Yii::$app->request->post('phone', '');

                if ($phone === '') {
                    Yii::$app->session->setFlash('error', 'Укажите номер телефона');
                    $viewModel = AuthorViewModel::fromDomainEntity($author);

                    return $this->render('subscribe', [
                        'model' => $viewModel,
                    ]);
                }

                try {
                    $handler = $this->getHandler(\app\services\Subscription\CreateSubscriptionHandler::class);
                    $dto = \app\dto\Subscription\CreateSubscriptionDTO::fromArray([
                        'phone' => $phone,
                        'author_id' => $id,
                    ]);
                    $command = new \app\domain\Subscription\Commands\CreateSubscriptionCommand($dto);

                    $subscription = $handler->handle($command);
                    Yii::$app->session->setFlash('success', 'Вы успешно подписались на уведомления!');
                } catch (InvalidArgumentException $e) {
                    if (str_contains($e->getMessage(), 'already exists')) {
                        Yii::$app->session->setFlash('info', 'Вы уже подписаны на этого автора');
                    } else {
                        Yii::$app->session->setFlash('error', 'Ошибка при создании подписки: ' . $e->getMessage());
                    }
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', 'Ошибка при создании подписки: ' . $e->getMessage());
                }

                return $this->redirect(['view', 'id' => $id]);
            }

            $viewModel = AuthorViewModel::fromDomainEntity($author);

            return $this->render('subscribe', [
                'model' => $viewModel,
            ]);
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            throw new NotFoundHttpException('Author not found');
        }
    }
}
