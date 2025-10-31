<?php

declare(strict_types=1);

namespace app\dto\Author;

final readonly class CreateAuthorDTO
{
    public function __construct(
        public string $fullName,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self((string) $data['full_name']);
    }

    public function toArray(): array
    {
        return ['full_name' => $this->fullName];
    }
}
