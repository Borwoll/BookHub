<?php

declare(strict_types=1);

namespace app\data;

final class TestData
{
    public static function getUsers(): array
    {
        return [
            [
                'username' => 'admin',
                'email' => 'admin@bookhub.com',
                'password' => 'admin123',
                'phone' => '+79161234567',
                'role' => \app\models\User::ROLE_USER,
            ],
            [
                'username' => 'user',
                'email' => 'user@bookhub.com',
                'password' => 'user123',
                'phone' => '+79162345678',
                'role' => \app\models\User::ROLE_USER,
            ],
        ];
    }

    public static function getAuthors(): array
    {
        return [
            'Лев Николаевич Толстой',
            'Федор Михайлович Достоевский',
            'Александр Сергеевич Пушкин',
            'Антон Павлович Чехов',
            'Иван Сергеевич Тургенев',
        ];
    }

    public static function getBooks(): array
    {
        return [
            [
                'title' => 'Война и мир',
                'year' => 1869,
                'description' => 'Эпический роман о событиях 1805-1820 годов во время наполеоновских войн',
                'isbn' => '978-5-389-02304-1',
                'authors' => [1],
            ],
            [
                'title' => 'Анна Каренина',
                'year' => 1877,
                'description' => 'Роман о трагической судьбе женщины в высшем обществе',
                'isbn' => '978-5-389-02305-8',
                'authors' => [1],
            ],
            [
                'title' => 'Преступление и наказание',
                'year' => 1866,
                'description' => 'Психологический роман о студенте Раскольникове',
                'isbn' => '978-5-389-02306-5',
                'authors' => [2],
            ],
            [
                'title' => 'Братья Карамазовы',
                'year' => 1880,
                'description' => 'Последний роман Достоевского о семье Карамазовых',
                'isbn' => '978-5-389-02307-2',
                'authors' => [2],
            ],
            [
                'title' => 'Евгений Онегин',
                'year' => 1833,
                'description' => 'Роман в стихах о дворянской жизни XIX века',
                'isbn' => '978-5-389-02308-9',
                'authors' => [3],
            ],
            [
                'title' => 'Капитанская дочка',
                'year' => 1836,
                'description' => 'Исторический роман о восстании Пугачева',
                'isbn' => '978-5-389-02309-6',
                'authors' => [3],
            ],
            [
                'title' => 'Вишневый сад',
                'year' => 1904,
                'description' => 'Пьеса о закате дворянства в России',
                'isbn' => '978-5-389-02310-2',
                'authors' => [4],
            ],
            [
                'title' => 'Отцы и дети',
                'year' => 1862,
                'description' => 'Роман о конфликте поколений',
                'isbn' => '978-5-389-02311-9',
                'authors' => [5],
            ],
        ];
    }
}
