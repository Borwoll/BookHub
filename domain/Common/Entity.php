<?php

declare(strict_types=1);

namespace app\domain\Common;

abstract class Entity
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function equals(Entity $entity): bool
    {
        return $this->getId() === $entity->getId()
            && static::class === $entity::class;
    }
}
