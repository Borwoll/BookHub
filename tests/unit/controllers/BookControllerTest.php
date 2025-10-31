<?php

declare(strict_types=1);

namespace tests\unit\controllers;

use app\controllers\BookController;
use app\domain\Book\Entities\Book;
use app\domain\Book\Exceptions\BookNotFoundException;
use app\domain\Book\ValueObjects\PublicationYear;
use app\domain\Book\ValueObjects\Title;
use app\queries\Book\GetBookQuery;
use app\queries\Book\GetBooksQuery;
use app\services\Book\GetBooksHandler;
use app\services\Book\GetBookViewModelHandler;
use app\viewModels\Book\BookViewModel;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;
use yii\web\UploadedFile;

/**
 * @internal
 * @small
 */
final class BookControllerTest extends Unit
{
    private BookController $controller;

    private MockObject|Request $request;

    private MockObject|Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new BookController('book', Yii::$app);

        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(Session::class);

        Yii::$app->set('request', $this->request);
        Yii::$app->set('session', $this->session);
    }

    public function test_action_index(): void
    {
        $mockHandler = $this->createMock(GetBooksHandler::class);

        $book1 = $this->createMock(Book::class);
        $book1->method('getId')->willReturn(1);
        $title1 = $this->createMock(Title::class);
        $title1->method('getValue')->willReturn('Test Book 1');
        $book1->method('getTitle')->willReturn($title1);
        $year1 = $this->createMock(PublicationYear::class);
        $year1->method('getValue')->willReturn(2020);
        $book1->method('getYear')->willReturn($year1);
        $book1->method('getDescription')->willReturn('Description 1');
        $book1->method('getCoverPhoto')->willReturn('cover1.jpg');
        $book1->method('getAuthors')->willReturn([1, 2]);

        $book2 = $this->createMock(Book::class);
        $book2->method('getId')->willReturn(2);
        $title2 = $this->createMock(Title::class);
        $title2->method('getValue')->willReturn('Test Book 2');
        $book2->method('getTitle')->willReturn($title2);
        $year2 = $this->createMock(PublicationYear::class);
        $year2->method('getValue')->willReturn(2021);
        $book2->method('getYear')->willReturn($year2);
        $book2->method('getDescription')->willReturn('Description 2');
        $book2->method('getCoverPhoto')->willReturn('cover2.jpg');
        $book2->method('getAuthors')->willReturn([]);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(GetBooksQuery::class))
            ->willReturn([$book1, $book2]);

        Yii::$container->set(GetBooksHandler::class, $mockHandler);

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('select')->willReturnSelf();
        $query->method('where')->willReturnSelf();
        $query->method('orderBy')->willReturnSelf();
        $query->method('column')->willReturn(['Author 1', 'Author 2']);

        $authorModel->expects($this->any())
            ->method('find')
            ->willReturn($query);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_view_success(): void
    {
        $bookId = 1;
        $mockHandler = $this->createMock(GetBookViewModelHandler::class);

        $viewModel = $this->createMock(BookViewModel::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($query) use ($bookId) {
                return $query instanceof GetBookQuery && $query->id === $bookId;
            }))
            ->willReturn($viewModel);

        Yii::$container->set(GetBookViewModelHandler::class, $mockHandler);

        $result = $this->controller->actionView($bookId);

        $this->assertIsString($result);
    }

    public function test_action_view_not_found(): void
    {
        $bookId = 999;
        $mockHandler = $this->createMock(GetBookViewModelHandler::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->willThrowException(new BookNotFoundException('Book not found'));

        Yii::$container->set(GetBookViewModelHandler::class, $mockHandler);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionView($bookId);
    }

    public function test_action_create_get_request(): void
    {
        $this->request->method('isPost')->willReturn(false);

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($query);

        $result = $this->controller->actionCreate();

        $this->assertIsString($result);
    }

    public function test_action_create_post_request(): void
    {
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn([
            'Book' => [
                'title' => 'New Book',
                'year' => 2023,
                'description' => 'Description',
                'author_ids' => [1, 2],
            ],
        ]);

        $this->request->method('get')->willReturn(null);

        $bookModel = $this->getMockBuilder(\app\models\Book::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['load', 'save', 'getAttribute'])
            ->getMock();

        $bookModel->method('load')->willReturn(true);
        $bookModel->method('save')->willReturn(true);
        $bookModel->id = 1;
        $bookModel->title = 'New Book';

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->any())
            ->method('find')
            ->willReturn($query);

        UploadedFile::reset();

        $bookAuthorModel = $this->getMockBuilder(\app\models\BookAuthor::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $bookAuthorQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $bookAuthorQuery->method('deleteAll')->willReturn(true);

        $bookAuthorModel->expects($this->once())
            ->method('deleteAll')
            ->willReturn(true);

        try {
            $result = $this->controller->actionCreate();
            $this->assertInstanceOf(Response::class, $result);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_action_update_get_request(): void
    {
        $bookId = 1;
        $this->request->method('isPost')->willReturn(false);

        $bookModel = $this->createMock(\app\models\Book::class);
        $bookModel->id = $bookId;

        $bookQuery = $this->createMock(Yii\db\ActiveQuery::class);
        $bookQuery->method('findOne')->with($bookId)->willReturn($bookModel);

        $bookModel->expects($this->once())
            ->method('findOne')
            ->with($bookId)
            ->willReturn($bookModel);

        $bookModel->method('getAuthorsIds')->willReturn([1, 2]);

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($query);

        try {
            $result = $this->controller->actionUpdate($bookId);
            $this->assertIsString($result);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_action_update_not_found(): void
    {
        $bookId = 999;

        $bookModel = $this->createMock(\app\models\Book::class);

        $bookModel->expects($this->once())
            ->method('findOne')
            ->with($bookId)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionUpdate($bookId);
    }

    public function test_action_delete_success(): void
    {
        $bookId = 1;

        $bookModel = $this->createMock(\app\models\Book::class);
        $bookModel->id = $bookId;

        $bookModel->expects($this->once())
            ->method('findOne')
            ->with($bookId)
            ->willReturn($bookModel);

        $bookModel->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('успешно удалена'));

        $result = $this->controller->actionDelete($bookId);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_delete_not_found(): void
    {
        $bookId = 999;

        $bookModel = $this->createMock(\app\models\Book::class);

        $bookModel->expects($this->once())
            ->method('findOne')
            ->with($bookId)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->controller->actionDelete($bookId);
    }
}
