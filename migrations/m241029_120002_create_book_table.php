<?php

declare(strict_types=1);

use yii\db\Migration;

class m241029_120002_create_book_table extends Migration
{
    public const string TABLE_NAME = '{{%book}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this
                ->primaryKey()
                ->comment('ID книги'),
            'title' => $this
                ->string()
                ->notNull()
                ->comment('Название книги'),
            'year' => $this
                ->integer()
                ->notNull()
                ->comment('Год выпуска'),
            'description' => $this
                ->text()
                ->comment('Описание книги'),
            'isbn' => $this
                ->string(17)
                ->unique()
                ->comment('ISBN номер'),
            'cover_photo' => $this
                ->string()
                ->comment('Фото обложки'),
            'created_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата создания'),
            'updated_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата обновления'),
        ]);

        $this->createIndex('idx-book-title', self::TABLE_NAME, 'title');
        $this->createIndex('idx-book-year', self::TABLE_NAME, 'year');
        $this->createIndex('idx-book-isbn', self::TABLE_NAME, 'isbn');
    }

    public function safeDown(): void
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
