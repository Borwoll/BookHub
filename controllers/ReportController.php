<?php

declare(strict_types=1);

namespace app\controllers;

use app\domain\Report\Services\ReportDomainService;
use app\queries\Report\GetTopAuthorsQuery;
use app\services\Report\GetTopAuthorsHandler;
use app\viewModels\Report\ReportViewModel;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class ReportController extends BaseController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'author', 'json'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $reportService = $this->getService(ReportDomainService::class);
        $availableYears = $reportService->getAvailableYears();
        $year = (int) Yii::$app->request->get('year', date('Y'));

        $authors = [];
        if ($year > 0 && in_array($year, $availableYears, true)) {
            try {
                $handler = $this->getHandler(GetTopAuthorsHandler::class);
                $query = new GetTopAuthorsQuery($year, 10);
                $topAuthors = $handler->handle($query);

                $authors = array_map(
                    static function ($dto): array {
                        if (is_array($dto)) {
                            return [
                                'id' => $dto['author_id'] ?? $dto['id'] ?? 0,
                                'full_name' => $dto['author_name'] ?? $dto['full_name'] ?? '',
                                'books_count' => $dto['books_count'] ?? 0,
                            ];
                        }

                        return [
                            'id' => $dto->authorId ?? 0,
                            'full_name' => $dto->authorName ?? '',
                            'books_count' => $dto->booksCount ?? 0,
                        ];
                    },
                    $topAuthors,
                );
            } catch (Throwable $e) {
                $this->handleDomainException($e);
            }
        }

        return $this->render('index', [
            'availableYears' => $availableYears,
            'year' => $year,
            'authors' => $authors,
        ]);
    }

    public function actionAuthor(): Response|string
    {
        $authorId = (int) Yii::$app->request->get('id', 0);
        $year = (int) Yii::$app->request->get('year', date('Y'));

        if ($authorId === 0) {
            Yii::$app->session->setFlash('error', 'Автор не указан');

            return $this->redirect(['index']);
        }

        try {
            $handler = $this->getHandler(\app\services\Report\GetAuthorReportHandler::class);
            $data = $handler->handle($authorId, $year);

            if ($data['author'] === null) {
                throw new NotFoundHttpException('Автор не найден');
            }

            return $this->render('author', [
                'author' => $data['author'],
                'year' => $data['year'],
                'books' => $data['books'],
                'booksCount' => $data['booksCount'],
            ]);
        } catch (Throwable $e) {
            $this->handleDomainException($e);
            Yii::$app->session->setFlash('error', 'Ошибка при загрузке данных автора');

            return $this->redirect(['index']);
        }
    }

    public function actionJson(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $year = (int) Yii::$app->request->get('year', date('Y'));
        $limit = (int) Yii::$app->request->get('limit', 10);

        try {
            $handler = $this->getHandler(GetTopAuthorsHandler::class);
            $query = new GetTopAuthorsQuery($year, $limit);
            $topAuthors = $handler->handle($query);

            $reportService = $this->getService(ReportDomainService::class);
            $availableYears = $reportService->getAvailableYears();

            $viewModel = ReportViewModel::fromReportData($year, $topAuthors, $availableYears);

            return [
                'success' => true,
                'data' => $viewModel->toArray(),
            ];
        } catch (Throwable $e) {
            $this->handleDomainException($e);

            return [
                'success' => false,
                'error' => 'Failed to generate report',
                'message' => $e->getMessage(),
            ];
        }
    }
}
