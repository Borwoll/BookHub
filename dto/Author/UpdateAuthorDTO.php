<?php

declare(strict_types=1);

namespace app\dto\Author;

final readonly class UpdateAuthorDTO
{
    public function __construct(
        public int $id,
        public ?string $fullName = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['full_name'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'full_name' => $this->fullName,
        ], fn($value) => $value !== null);
    }
}
