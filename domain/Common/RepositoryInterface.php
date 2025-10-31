<?php

declare(strict_types=1);

namespace app\domain\Common;

interface RepositoryInterface
{
    public function findById(int $id): mixed;

    public function save(mixed $entity): bool;

    public function delete(mixed $entity): bool;

    public function exists(int $id): bool;
}
