<?php

declare(strict_types=1);

namespace tests\unit\controllers;

use app\controllers\ReportController;
use app\domain\Report\Services\ReportDomainService;
use app\queries\Report\GetTopAuthorsQuery;
use app\services\Report\GetTopAuthorsHandler;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;

/**
 * @internal
 * @small
 */
final class ReportControllerTest extends Unit
{
    private ReportController $controller;

    private MockObject|Request $request;

    private MockObject|Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new ReportController('report', Yii::$app);

        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(Session::class);

        Yii::$app->set('request', $this->request);
        Yii::$app->set('session', $this->session);
    }

    public function test_action_index_with_default_year(): void
    {
        $currentYear = (int) date('Y');
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($currentYear) {
            return $param === 'year' ? $default ?? $currentYear : $default;
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, $currentYear]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $mockHandler = $this->createMock(GetTopAuthorsHandler::class);

        $topAuthors = [
            ['author_id' => 1, 'author_name' => 'Author 1', 'books_count' => 5],
            ['author_id' => 2, 'author_name' => 'Author 2', 'books_count' => 3],
        ];

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($currentYear) {
                return $query instanceof GetTopAuthorsQuery
                    && $query->year === $currentYear
                    && $query->limit === 10;
            }))
            ->willReturn($topAuthors);

        Yii::$container->set(GetTopAuthorsHandler::class, $mockHandler);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_index_with_specific_year(): void
    {
        $year = 2022;
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($year) {
            return $param === 'year' ? $year : $default;
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, 2023]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $mockHandler = $this->createMock(GetTopAuthorsHandler::class);

        $topAuthors = [
            ['author_id' => 1, 'author_name' => 'Author 1', 'books_count' => 5],
        ];

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($year) {
                return $query instanceof GetTopAuthorsQuery && $query->year === $year;
            }))
            ->willReturn($topAuthors);

        Yii::$container->set(GetTopAuthorsHandler::class, $mockHandler);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_index_with_invalid_year(): void
    {
        $year = 1900;
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($year) {
            return $param === 'year' ? $year : $default;
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, 2023]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_index_with_handler_error(): void
    {
        $year = 2022;
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($year) {
            return $param === 'year' ? $year : $default;
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, 2023]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $mockHandler = $this->createMock(GetTopAuthorsHandler::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->willThrowException(new Exception('Handler error'));

        Yii::$container->set(GetTopAuthorsHandler::class, $mockHandler);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_author_success(): void
    {
        $authorId = 1;
        $year = 2022;
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($authorId, $year) {
            return match ($param) {
                'id' => $authorId,
                'year' => $year,
                default => $default,
            };
        });

        $authorModel = $this->createMock(\app\models\Author::class);
        $authorModel->id = $authorId;
        $authorModel->full_name = 'Test Author';

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturnSelf();

        $authorQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $authorQuery->method('with')->willReturnSelf();
        $authorQuery->method('where')->willReturnSelf();
        $authorQuery->method('one')->willReturn($authorModel);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($authorQuery);

        $bookModel = $this->getMockBuilder(\app\models\Book::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $bookQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $bookQuery->method('innerJoin')->willReturnSelf();
        $bookQuery->method('where')->willReturnSelf();
        $bookQuery->method('with')->willReturnSelf();
        $bookQuery->method('all')->willReturn([]);

        $bookModel->expects($this->once())
            ->method('find')
            ->willReturn($bookQuery);

        $result = $this->controller->actionAuthor();

        $this->assertIsString($result);
    }

    public function test_action_author_without_author_id(): void
    {
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) {
            return match ($param) {
                'id' => 0,
                'year' => $default ?? (int) date('Y'),
                default => $default,
            };
        });

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('Автор не указан'));

        $result = $this->controller->actionAuthor();

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_author_not_found(): void
    {
        $authorId = 999;
        $year = 2022;
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($authorId, $year) {
            return match ($param) {
                'id' => $authorId,
                'year' => $year,
                default => $default,
            };
        });

        $authorQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $authorQuery->method('with')->willReturnSelf();
        $authorQuery->method('where')->willReturnSelf();
        $authorQuery->method('one')->willReturn(null);

        $authorModel = $this->createMock(\app\models\Author::class);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($authorQuery);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionAuthor();
    }

    public function test_action_author_with_error(): void
    {
        $authorId = 1;
        $year = 2022;
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($authorId, $year) {
            return match ($param) {
                'id' => $authorId,
                'year' => $year,
                default => $default,
            };
        });

        $authorQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $authorQuery->method('with')->willReturnSelf();
        $authorQuery->method('where')->willReturnSelf();
        $authorQuery->method('one')->willThrowException(new Exception('Database error'));

        $authorModel = $this->createMock(\app\models\Author::class);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($authorQuery);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('Ошибка'));

        $result = $this->controller->actionAuthor();

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_json_success(): void
    {
        $year = 2022;
        $limit = 10;

        $response = $this->createMock(Response::class);
        $response->format = Response::FORMAT_JSON;
        Yii::$app->set('response', $response);

        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($year, $limit) {
            return match ($param) {
                'year' => $year,
                'limit' => $limit,
                default => $default,
            };
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, 2023]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $mockHandler = $this->createMock(GetTopAuthorsHandler::class);

        $topAuthors = [
            ['author_id' => 1, 'author_name' => 'Author 1', 'books_count' => 5],
            ['author_id' => 2, 'author_name' => 'Author 2', 'books_count' => 3],
        ];

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($year, $limit) {
                return $query instanceof GetTopAuthorsQuery
                    && $query->year === $year
                    && $query->limit === $limit;
            }))
            ->willReturn($topAuthors);

        Yii::$container->set(GetTopAuthorsHandler::class, $mockHandler);

        $result = $this->controller->actionJson();

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_action_json_with_default_parameters(): void
    {
        $currentYear = (int) date('Y');
        $defaultLimit = 10;

        $response = $this->createMock(Response::class);
        $response->format = Response::FORMAT_JSON;
        Yii::$app->set('response', $response);

        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($currentYear, $defaultLimit) {
            return match ($param) {
                'year' => $default ?? $currentYear,
                'limit' => $default ?? $defaultLimit,
                default => $default,
            };
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, $currentYear]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $mockHandler = $this->createMock(GetTopAuthorsHandler::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($currentYear, $defaultLimit) {
                return $query instanceof GetTopAuthorsQuery
                    && $query->year === $currentYear
                    && $query->limit === $defaultLimit;
            }))
            ->willReturn([]);

        Yii::$container->set(GetTopAuthorsHandler::class, $mockHandler);

        $result = $this->controller->actionJson();

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }

    public function test_action_json_with_error(): void
    {
        $year = 2022;

        $response = $this->createMock(Response::class);
        $response->format = Response::FORMAT_JSON;
        Yii::$app->set('response', $response);

        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($year) {
            return match ($param) {
                'year' => $year,
                'limit' => $default ?? 10,
                default => $default,
            };
        });

        $mockService = $this->createMock(ReportDomainService::class);
        $mockService->method('getAvailableYears')->willReturn([2020, 2021, 2022, 2023]);

        Yii::$container->set(ReportDomainService::class, $mockService);

        $mockHandler = $this->createMock(GetTopAuthorsHandler::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->willThrowException(new Exception('Handler error'));

        Yii::$container->set(GetTopAuthorsHandler::class, $mockHandler);

        $result = $this->controller->actionJson();

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result);
    }
}
