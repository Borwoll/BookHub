<?php

declare(strict_types=1);

namespace app\domain\Book\Entities;

use app\domain\Book\Events\BookCreated;
use app\domain\Book\Events\BookUpdated;
use app\domain\Book\ValueObjects\BookId;
use app\domain\Book\ValueObjects\ISBN;
use app\domain\Book\ValueObjects\PublicationYear;
use app\domain\Book\ValueObjects\Title;
use app\domain\Common\Entity;

final class Book extends Entity
{
    private array $domainEvents = [];

    public function __construct(
        private Title $title,
        private PublicationYear $year,
        private ?string $description = null,
        private ?ISBN $isbn = null,
        private ?string $coverPhoto = null,
        private array $authors = [],
        ?BookId $id = null,
    ) {
        if ($id !== null) {
            $this->setId($id->getValue());
        }
    }

    public static function create(
        Title $title,
        PublicationYear $year,
        ?string $description = null,
        ?ISBN $isbn = null,
        array $authors = [],
    ): self {
        $book = new self($title, $year, $description, $isbn, null, $authors);
        $book->addDomainEvent(new BookCreated($book));

        return $book;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getYear(): PublicationYear
    {
        return $this->year;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIsbn(): ?ISBN
    {
        return $this->isbn;
    }

    public function getCoverPhoto(): ?string
    {
        return $this->coverPhoto;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function updateTitle(Title $title): void
    {
        $this->title = $title;
        $this->addDomainEvent(new BookUpdated($this->getId(), 'title_updated'));
    }

    public function updateYear(PublicationYear $year): void
    {
        $this->year = $year;
        $this->addDomainEvent(new BookUpdated($this->getId(), 'year_updated'));
    }

    public function updateDescription(?string $description): void
    {
        $this->description = $description;
        $this->addDomainEvent(new BookUpdated($this->getId(), 'description_updated'));
    }

    public function updateIsbn(?ISBN $isbn): void
    {
        $this->isbn = $isbn;
        $this->addDomainEvent(new BookUpdated($this->getId(), 'isbn_updated'));
    }

    public function setCoverPhoto(?string $coverPhoto): void
    {
        $this->coverPhoto = $coverPhoto;
        $this->addDomainEvent(new BookUpdated($this->getId(), 'cover_photo_updated'));
    }

    public function addAuthor(int $authorId): void
    {
        if (in_array($authorId, $this->authors, true) === false) {
            $this->authors[] = $authorId;
            $this->addDomainEvent(new BookUpdated($this->getId(), 'author_added'));
        }
    }

    public function removeAuthor(int $authorId): void
    {
        $key = array_search($authorId, $this->authors, true);
        if ($key !== false) {
            unset($this->authors[$key]);
            $this->authors = array_values($this->authors);
            $this->addDomainEvent(new BookUpdated($this->getId(), 'author_removed'));
        }
    }

    public function setAuthors(array $authors): void
    {
        $this->authors = $authors;
        $this->addDomainEvent(new BookUpdated($this->getId(), 'authors_updated'));
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->title->getValue(),
            'year' => $this->year->getValue(),
            'description' => $this->description,
            'isbn' => $this->isbn?->getValue(),
            'cover_photo' => $this->coverPhoto,
            'authors' => $this->authors,
        ];
    }

    private function addDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
