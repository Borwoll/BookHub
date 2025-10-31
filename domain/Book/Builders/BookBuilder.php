<?php

declare(strict_types=1);

namespace app\domain\Book\Builders;

use app\domain\Book\Entities\Book;
use app\domain\Book\ValueObjects\BookId;
use app\domain\Book\ValueObjects\ISBN;
use app\domain\Book\ValueObjects\PublicationYear;
use app\domain\Book\ValueObjects\Title;
use InvalidArgumentException;

final class BookBuilder
{
    private ?Title $title = null;

    private ?PublicationYear $year = null;

    private ?string $description = null;

    private ?ISBN $isbn = null;

    private ?string $coverPhoto = null;

    private array $authors = [];

    private ?BookId $id = null;

    public static function create(): self
    {
        return new self();
    }

    public function withTitle(string $title): self
    {
        $this->title = new Title($title);

        return $this;
    }

    public function withYear(int $year): self
    {
        $this->year = new PublicationYear($year);

        return $this;
    }

    public function withDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function withIsbn(?string $isbn): self
    {
        $this->isbn = ($isbn !== null && $isbn !== '') ? new ISBN($isbn) : null;

        return $this;
    }

    public function withCoverPhoto(?string $coverPhoto): self
    {
        $this->coverPhoto = $coverPhoto;

        return $this;
    }

    public function withAuthors(array $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function withAuthor(int $authorId): self
    {
        $this->authors[] = $authorId;

        return $this;
    }

    public function withId(int $id): self
    {
        $this->id = new BookId($id);

        return $this;
    }

    public function build(): Book
    {
        if ($this->title === null || $this->year === null) {
            throw new InvalidArgumentException('Title and year are required');
        }

        return new Book(
            $this->title,
            $this->year,
            $this->description,
            $this->isbn,
            $this->coverPhoto,
            $this->authors,
            $this->id,
        );
    }

    public function reset(): self
    {
        $this->title = null;
        $this->year = null;
        $this->description = null;
        $this->isbn = null;
        $this->coverPhoto = null;
        $this->authors = [];
        $this->id = null;

        return $this;
    }
}
