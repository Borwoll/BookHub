<?php

declare(strict_types=1);

namespace app\domain\Book\Factories;

use app\domain\Book\Entities\Book;
use app\domain\Book\ValueObjects\BookId;
use app\domain\Book\ValueObjects\ISBN;
use app\domain\Book\ValueObjects\PublicationYear;
use app\domain\Book\ValueObjects\Title;

final class BookFactory
{
    public static function create(array $data): Book
    {
        $title = new Title($data['title']);
        $year = new PublicationYear($data['year']);
        $isbn = ! empty($data['isbn']) ? new ISBN($data['isbn']) : null;
        $authors = $data['authors'] ?? [];

        return Book::create(
            $title,
            $year,
            $data['description'] ?? null,
            $isbn,
            $authors,
        );
    }

    public static function fromArray(array $data): Book
    {
        $title = new Title($data['title']);
        $year = new PublicationYear($data['year']);
        $isbn = ! empty($data['isbn']) ? new ISBN($data['isbn']) : null;
        $id = isset($data['id']) ? new BookId($data['id']) : null;

        return new Book(
            $title,
            $year,
            $data['description'] ?? null,
            $isbn,
            $data['cover_photo'] ?? null,
            $data['authors'] ?? [],
            $id,
        );
    }

    public static function fromActiveRecord(\app\models\Book $activeRecord): Book
    {
        $title = new Title($activeRecord->title);
        $year = new PublicationYear($activeRecord->year);
        $isbn = ! empty($activeRecord->isbn) ? new ISBN($activeRecord->isbn) : null;
        $id = new BookId($activeRecord->id);

        $book = new Book(
            $title,
            $year,
            $activeRecord->description,
            $isbn,
            $activeRecord->cover_photo,
            [],
            $id,
        );

        if ($activeRecord->authors) {
            foreach ($activeRecord->authors as $author) {
                $book->addAuthor($author->id);
            }
        }

        return $book;
    }
}
