<?php

declare(strict_types=1);

namespace app\domain\Author\Factories;

use app\domain\Author\Entities\Author;
use app\domain\Author\ValueObjects\AuthorId;
use app\domain\Author\ValueObjects\AuthorName;

final class AuthorFactory
{
    public static function create(array $data): Author
    {
        $name = new AuthorName($data['full_name']);

        return Author::create($name);
    }

    public static function fromArray(array $data): Author
    {
        $name = new AuthorName($data['full_name']);
        $id = isset($data['id']) ? new AuthorId($data['id']) : null;

        return new Author($name, $id);
    }

    public static function fromActiveRecord(\app\models\Author $activeRecord): Author
    {
        $name = new AuthorName($activeRecord->full_name);
        $id = new AuthorId($activeRecord->id);

        return new Author($name, $id);
    }
}
