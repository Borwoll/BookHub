<?php

declare(strict_types=1);

namespace app\commands;

use app\data\TestData;
use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;
use app\models\User;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

final class TestDataController extends Controller
{
    private array $authorIdByIndex = [];

    public function actionGenerate(): int
    {
        $this->stdout("Начинаем заполнение тестовыми данными...\n");

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->createUsers();
            $this->createAuthors();
            $this->createBooks();

            $transaction->commit();
            $this->stdout("Тестовые данные успешно созданы!\n");

            return ExitCode::OK;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->stderr('Ошибка при создании данных: ' . $e->getMessage() . "\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    public function actionClear(): int
    {
        $this->stdout("Очищаем тестовые данные...\n");

        $transaction = Yii::$app->db->beginTransaction();

        try {
            BookAuthor::deleteAll();
            Book::deleteAll();
            Author::deleteAll();
            User::deleteAll();

            $transaction->commit();
            $this->stdout("Тестовые данные очищены!\n");

            return ExitCode::OK;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->stderr('Ошибка при очистке данных: ' . $e->getMessage() . "\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    private function createUsers(): void
    {
        $this->stdout("Создание пользователей...\n");

        $users = TestData::getUsers();

        foreach ($users as $userData) {
            $user = new User();
            $user->username = $userData['username'];
            $user->email = $userData['email'];
            $user->phone = $userData['phone'];
            $user->role = $userData['role'];
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($userData['password']);
            $user->generateAuthKey();

            if (false === $user->save()) {
                throw new Exception('Не удалось создать пользователя: ' . json_encode($user->errors));
            }

            $this->stdout("Создан пользователь: {$user->username}\n");
        }
    }

    private function createAuthors(): void
    {
        $this->stdout("Создание авторов...\n");

        $authors = TestData::getAuthors();

        foreach ($authors as $i => $authorName) {
            $author = new Author();
            $author->full_name = $authorName;

            if (false === $author->save()) {
                throw new Exception('Не удалось создать автора: ' . json_encode($author->errors));
            }

            $this->stdout("Создан автор: {$author->full_name}\n");

            $this->authorIdByIndex[$i + 1] = $author->id;
        }
    }

    private function createBooks(): void
    {
        $this->stdout("Создание книг...\n");

        $books = TestData::getBooks();

        foreach ($books as $bookData) {
            $book = new Book();
            $book->title = $bookData['title'];
            $book->year = $bookData['year'];
            $book->description = $bookData['description'];
            $book->isbn = $bookData['isbn'];

            if (false === $book->save()) {
                throw new Exception('Не удалось создать книгу: ' . json_encode($book->errors));
            }

            foreach ($bookData['authors'] as $index) {
                $authorId = $this->authorIdByIndex[$index] ?? null;
                if (null === $authorId) {
                    throw new Exception('Не удалось найти ID автора по индексу: ' . $index);
                }
                $bookAuthor = new BookAuthor();
                $bookAuthor->book_id = $book->id;
                $bookAuthor->author_id = $authorId;

                if (false === $bookAuthor->save()) {
                    throw new Exception('Не удалось связать книгу с автором: ' . json_encode($bookAuthor->errors));
                }
            }

            $this->stdout("Создана книга: {$book->title}\n");
        }
    }
}
