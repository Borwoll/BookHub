<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

final class Book extends ActiveRecord
{
    public null|string|UploadedFile $photo_file = null;

    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => date('Y') + 10],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 17],
            [['isbn'], 'unique'],
            [['cover_photo'], 'string', 'max' => 255],
            [['title', 'description'], 'trim'],
            [['photo_file'], 'file', 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_photo' => 'Фото обложки',
            'photo_file' => 'Загрузить фото обложки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    public function getBookAuthors(): ActiveQuery
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    public function getAuthorsNames(): string
    {
        $authors = $this->authors;
        $names = [];
        foreach ($authors as $author) {
            $names[] = $author->full_name;
        }

        return implode(', ', $names);
    }

    public function getAuthorsIds(): array
    {
        $authors = $this->authors;
        $ids = [];
        foreach ($authors as $author) {
            $ids[] = $author->id;
        }

        return $ids;
    }
}
