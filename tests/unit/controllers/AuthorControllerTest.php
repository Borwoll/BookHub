<?php

declare(strict_types=1);

namespace tests\unit\controllers;

use app\commands\Author\CreateAuthorCommand;
use app\controllers\AuthorController;
use app\domain\Author\Entities\Author;
use app\domain\Subscription\Commands\CreateSubscriptionCommand;
use app\queries\Author\GetAllAuthorsQuery;
use app\queries\Author\GetAuthorQuery;
use app\services\Author\CreateAuthorHandler;
use app\services\Author\GetAllAuthorsHandler;
use app\services\Author\GetAuthorHandler;
use app\services\Author\GetAuthorViewDataHandler;
use app\services\Subscription\CreateSubscriptionHandler;
use app\viewModels\Author\AuthorViewModel;
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
final class AuthorControllerTest extends Unit
{
    private AuthorController $controller;

    private MockObject|Request $request;

    private MockObject|Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new AuthorController('author', Yii::$app);

        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(Session::class);

        Yii::$app->set('request', $this->request);
        Yii::$app->set('session', $this->session);
    }

    public function test_action_index_without_search(): void
    {
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) {
            return $param === 'search' ? '' : $default;
        });

        $mockHandler = $this->createMock(GetAllAuthorsHandler::class);

        $author1 = $this->createMock(Author::class);
        $author1->method('getId')->willReturn(1);

        $author2 = $this->createMock(Author::class);
        $author2->method('getId')->willReturn(2);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(GetAllAuthorsQuery::class))
            ->willReturn([$author1, $author2]);

        Yii::$container->set(GetAllAuthorsHandler::class, $mockHandler);

        $bookModel = $this->getMockBuilder(\app\models\Book::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $bookQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $bookQuery->method('innerJoin')->willReturnSelf();
        $bookQuery->method('where')->willReturnSelf();
        $bookQuery->method('count')->willReturn(5);

        $bookModel->expects($this->any())
            ->method('find')
            ->willReturn($bookQuery);

        $subscriptionModel = $this->getMockBuilder(\app\models\Subscription::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $subscriptionQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $subscriptionQuery->method('where')->willReturnSelf();
        $subscriptionQuery->method('count')->willReturn(3);

        $subscriptionModel->expects($this->any())
            ->method('find')
            ->willReturn($subscriptionQuery);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_index_with_search(): void
    {
        $searchQuery = 'Tolstoy';
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($searchQuery) {
            return $param === 'search' ? $searchQuery : $default;
        });

        $mockHandler = $this->createMock(GetAllAuthorsHandler::class);

        $author = $this->createMock(Author::class);
        $author->method('getId')->willReturn(1);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($searchQuery) {
                return $query instanceof GetAllAuthorsQuery && $query->searchQuery === $searchQuery;
            }))
            ->willReturn([$author]);

        Yii::$container->set(GetAllAuthorsHandler::class, $mockHandler);

        $bookModel = $this->getMockBuilder(\app\models\Book::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $bookQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $bookQuery->method('innerJoin')->willReturnSelf();
        $bookQuery->method('where')->willReturnSelf();
        $bookQuery->method('count')->willReturn(2);

        $bookModel->expects($this->any())
            ->method('find')
            ->willReturn($bookQuery);

        $subscriptionModel = $this->getMockBuilder(\app\models\Subscription::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $subscriptionQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $subscriptionQuery->method('where')->willReturnSelf();
        $subscriptionQuery->method('count')->willReturn(1);

        $subscriptionModel->expects($this->any())
            ->method('find')
            ->willReturn($subscriptionQuery);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_view_success(): void
    {
        $authorId = 1;

        $mockHandler = $this->createMock(GetAuthorViewDataHandler::class);

        $authorModel = $this->createMock(\app\models\Author::class);
        $viewModel = $this->createMock(AuthorViewModel::class);
        $books = [['title' => 'Book 1', 'year' => 2020]];

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($authorId) {
                return $query instanceof GetAuthorQuery && $query->authorId === $authorId;
            }))
            ->willReturn([
                'activeRecord' => $authorModel,
                'viewModel' => $viewModel,
                'books' => $books,
            ]);

        Yii::$container->set(GetAuthorViewDataHandler::class, $mockHandler);

        $result = $this->controller->actionView($authorId);

        $this->assertIsString($result);
    }

    public function test_action_view_not_found(): void
    {
        $authorId = 999;

        $mockHandler = $this->createMock(GetAuthorViewDataHandler::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->willThrowException(new Exception('Author not found'));

        Yii::$container->set(GetAuthorViewDataHandler::class, $mockHandler);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionView($authorId);
    }

    public function test_action_create_get_request(): void
    {
        $this->request->method('isPost')->willReturn(false);

        $result = $this->controller->actionCreate();

        $this->assertIsString($result);
    }

    public function test_action_create_post_request(): void
    {
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn([
            'full_name' => 'New Author',
        ]);

        $mockHandler = $this->createMock(CreateAuthorHandler::class);

        $author = $this->createMock(Author::class);
        $author->method('getId')->willReturn(1);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateAuthorCommand::class))
            ->willReturn($author);

        Yii::$container->set(CreateAuthorHandler::class, $mockHandler);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('успешно создан'));

        $result = $this->controller->actionCreate();

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_update_get_request(): void
    {
        $authorId = 1;
        $this->request->method('isPost')->willReturn(false);

        $authorModel = $this->createMock(\app\models\Author::class);
        $authorModel->id = $authorId;

        $authorModel->expects($this->once())
            ->method('findOne')
            ->with($authorId)
            ->willReturn($authorModel);

        $result = $this->controller->actionUpdate($authorId);

        $this->assertIsString($result);
    }

    public function test_action_update_post_request(): void
    {
        $authorId = 1;
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn([
            'Author' => [
                'full_name' => 'Updated Author',
            ],
        ]);

        $authorModel = $this->createMock(\app\models\Author::class);
        $authorModel->id = $authorId;

        $authorModel->expects($this->once())
            ->method('findOne')
            ->with($authorId)
            ->willReturn($authorModel);

        $authorModel->expects($this->once())
            ->method('load')
            ->willReturn(true);

        $authorModel->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('успешно обновлен'));

        $result = $this->controller->actionUpdate($authorId);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_update_not_found(): void
    {
        $authorId = 999;

        $authorModel = $this->createMock(\app\models\Author::class);

        $authorModel->expects($this->once())
            ->method('findOne')
            ->with($authorId)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionUpdate($authorId);
    }

    public function test_action_delete_success(): void
    {
        $authorId = 1;

        $authorModel = $this->createMock(\app\models\Author::class);
        $authorModel->id = $authorId;

        $authorModel->expects($this->once())
            ->method('findOne')
            ->with($authorId)
            ->willReturn($authorModel);

        $authorModel->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('успешно удален'));

        $result = $this->controller->actionDelete($authorId);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_delete_not_found(): void
    {
        $authorId = 999;

        $authorModel = $this->createMock(\app\models\Author::class);

        $authorModel->expects($this->once())
            ->method('findOne')
            ->with($authorId)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionDelete($authorId);
    }

    public function test_action_subscribe_get_request(): void
    {
        $authorId = 1;
        $this->request->method('isPost')->willReturn(false);

        $mockHandler = $this->createMock(GetAuthorHandler::class);

        $author = $this->createMock(Author::class);
        $author->method('getId')->willReturn($authorId);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(GetAuthorQuery::class))
            ->willReturn($author);

        Yii::$container->set(GetAuthorHandler::class, $mockHandler);

        $result = $this->controller->actionSubscribe($authorId);

        $this->assertIsString($result);
    }

    public function test_action_subscribe_post_request_with_phone(): void
    {
        $authorId = 1;
        $phone = '+79991234567';
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn(['phone' => $phone]);

        $getAuthorHandler = $this->createMock(GetAuthorHandler::class);
        $author = $this->createMock(Author::class);
        $author->method('getId')->willReturn($authorId);

        $getAuthorHandler->expects($this->once())
            ->method('handle')
            ->willReturn($author);

        Yii::$container->set(GetAuthorHandler::class, $getAuthorHandler);

        $createSubscriptionHandler = $this->createMock(CreateSubscriptionHandler::class);
        $subscription = $this->createMock(\app\domain\Subscription\Entities\Subscription::class);

        $createSubscriptionHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateSubscriptionCommand::class))
            ->willReturn($subscription);

        Yii::$container->set(CreateSubscriptionHandler::class, $createSubscriptionHandler);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('подписались'));

        $result = $this->controller->actionSubscribe($authorId);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_subscribe_post_request_without_phone(): void
    {
        $authorId = 1;
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn(['phone' => '']);

        $getAuthorHandler = $this->createMock(GetAuthorHandler::class);
        $author = $this->createMock(Author::class);
        $author->method('getId')->willReturn($authorId);

        $getAuthorHandler->expects($this->once())
            ->method('handle')
            ->willReturn($author);

        Yii::$container->set(GetAuthorHandler::class, $getAuthorHandler);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('номер телефона'));

        $result = $this->controller->actionSubscribe($authorId);

        $this->assertIsString($result);
    }

    public function test_action_subscribe_not_found(): void
    {
        $authorId = 999;

        $getAuthorHandler = $this->createMock(GetAuthorHandler::class);

        $getAuthorHandler->expects($this->once())
            ->method('handle')
            ->willReturn(null);

        Yii::$container->set(GetAuthorHandler::class, $getAuthorHandler);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionSubscribe($authorId);
    }
}
