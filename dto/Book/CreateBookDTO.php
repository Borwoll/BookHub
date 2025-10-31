<?php

declare(strict_types=1);

namespace app\dto\Book;

final readonly class CreateBookDTO
{
    public function __construct(
        public string $title,
        public int $year,
        public ?string $description = null,
        public ?string $isbn = null,
        public array $authorIds = [],
        public ?string $coverPhoto = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['title'],
            (int) $data['year'],
            $data['description'] ?? null,
            $data['isbn'] ?? null,
            is_array($data['author_ids'] ?? null) ? $data['author_ids'] : [],
            $data['cover_photo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'year' => $this->year,
            'description' => $this->description,
            'isbn' => $this->isbn,
            'author_ids' => $this->authorIds,
            'cover_photo' => $this->coverPhoto,
        ];
    }
}
