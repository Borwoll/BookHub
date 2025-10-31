<?php

declare(strict_types=1);

namespace app\viewModels\Book;

use app\domain\Book\Entities\Book;

final readonly class BookViewModel
{
    public function __construct(
        public int $id,
        public string $title,
        public int $year,
        public string $description,
        public string $isbn,
        public ?string $coverPhoto = null,
        public array $authorIds = [],
        public string $authorsNames = '',
        public array $authors = [],
        public ?int $createdAt = null,
        public ?int $updatedAt = null,
    ) {}

    public static function fromDomainEntity(Book $book, array $authors = [], ?int $createdAt = null, ?int $updatedAt = null): self
    {
        $authorIds = $book->getAuthors();
        $authorsNames = '';

        if ($authors !== []) {
            $names = [];
            foreach ($authors as $author) {
                if (is_object($author)) {
                    if (method_exists($author, 'getFullName')) {
                        $names[] = $author->getFullName();
                    } elseif (method_exists($author, '__get') || $author instanceof \yii\db\ActiveRecord) {
                        $names[] = $author->full_name ?? '';
                    } elseif (property_exists($author, 'full_name')) {
                        $names[] = $author->full_name;
                    }
                } elseif (is_string($author)) {
                    $names[] = $author;
                }
            }
            $names = array_filter($names);
            $authorsNames = implode(', ', $names);
        }

        return new self(
            $book->getId() ?? 0,
            $book->getTitle()->getValue(),
            $book->getYear()->getValue(),
            $book->getDescription() ?? '',
            $book->getIsbn()?->getValue() ?? '',
            $book->getCoverPhoto(),
            $authorIds,
            $authorsNames,
            $authors,
            $createdAt,
            $updatedAt,
        );
    }

    public function getAuthorsNames(): string
    {
        if ($this->authorsNames === '' && $this->authors !== []) {
            $names = [];
            foreach ($this->authors as $author) {
                if (is_object($author)) {
                    if (method_exists($author, 'getFullName')) {
                        $names[] = $author->getFullName();
                    } elseif ($author instanceof \yii\db\ActiveRecord) {
                        $names[] = $author->full_name ?? '';
                    } elseif (property_exists($author, 'full_name')) {
                        $names[] = $author->full_name;
                    }
                } elseif (is_string($author)) {
                    $names[] = $author;
                }
            }
            $names = array_filter($names);

            return implode(', ', $names);
        }

        return $this->authorsNames;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'description' => $this->description,
            'isbn' => $this->isbn,
            'cover_photo' => $this->coverPhoto,
            'author_ids' => $this->authorIds,
            'authors_names' => $this->authorsNames,
        ];
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'cover_photo' => $this->coverPhoto,
            'author_ids' => $this->authorIds,
            'authors_names' => $this->authorsNames,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'authors' => $this->authors,
            default => null,
        };
    }
}
