<?php

declare(strict_types=1);

namespace app\repositories;

use app\domain\Common\Entity;
use app\domain\Common\RepositoryInterface;
use InvalidArgumentException;

abstract class BaseRepository implements RepositoryInterface
{
    public function findById(int $id): ?Entity
    {
        return $this->doFindById($id);
    }

    public function save(mixed $entity): bool
    {
        if (! ($entity instanceof Entity)) {
            throw new InvalidArgumentException('Entity must be an instance of Domain\Common\Entity');
        }

        return $this->doSave($entity);
    }

    public function delete(mixed $entity): bool
    {
        if (! ($entity instanceof Entity)) {
            throw new InvalidArgumentException('Entity must be an instance of Domain\Common\Entity');
        }

        return $this->doDelete($entity);
    }

    public function exists(int $id): bool
    {
        return $this->doExists($id);
    }

    abstract protected function doFindById(int $id): ?Entity;

    abstract protected function doSave(Entity $entity): bool;

    abstract protected function doDelete(Entity $entity): bool;

    abstract protected function doExists(int $id): bool;
}
