<?php

declare(strict_types=1);

namespace app\domain\Author\Entities;

use app\domain\Author\Events\AuthorCreated;
use app\domain\Author\Events\AuthorUpdated;
use app\domain\Author\ValueObjects\AuthorId;
use app\domain\Author\ValueObjects\AuthorName;
use app\domain\Common\Entity;

final class Author extends Entity
{
    private array $domainEvents = [];

    public function __construct(
        private AuthorName $name,
        ?AuthorId $id = null,
    ) {
        if ($id !== null) {
            $this->setId($id->getValue());
        }
    }

    public static function create(AuthorName $name): self
    {
        $author = new self($name);
        $author->addDomainEvent(new AuthorCreated($author));

        return $author;
    }

    public function getName(): AuthorName
    {
        return $this->name;
    }

    public function updateName(AuthorName $name): void
    {
        $this->name = $name;
        $this->addDomainEvent(new AuthorUpdated($this->getId(), 'name_updated'));
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
            'full_name' => $this->name->getValue(),
        ];
    }

    private function addDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
