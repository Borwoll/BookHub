<?php

declare(strict_types=1);

namespace app\dto\Book;

final readonly class UpdateBookDTO
{
    public function __construct(
        public int $id,
        public ?string $title = null,
        public ?int $year = null,
        public ?string $description = null,
        public ?string $isbn = null,
        public ?array $authorIds = null,
        public ?string $coverPhoto = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['title'] ?? null,
            array_key_exists('year', $data) ? (int) $data['year'] : null,
            $data['description'] ?? null,
            $data['isbn'] ?? null,
            isset($data['author_ids']) && is_array($data['author_ids']) ? $data['author_ids'] : null,
            $data['cover_photo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'description' => $this->description,
            'isbn' => $this->isbn,
            'author_ids' => $this->authorIds,
            'cover_photo' => $this->coverPhoto,
        ], fn($value) => $value !== null);
    }
}
