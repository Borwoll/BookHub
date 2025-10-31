<?php

declare(strict_types=1);

use yii\db\Migration;

class m241029_120003_create_book_author_table extends Migration
{
    public const string TABLE_NAME = '{{%book_author}}';

    public const string BOOK_TABLE_NAME = '{{%book}}';

    public const string AUTHOR_TABLE_NAME = '{{%author}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this
                ->primaryKey()
                ->comment('ID связи'),
            'book_id' => $this
                ->integer()
                ->notNull()
                ->comment('ID книги'),
            'author_id' => $this
                ->integer()
                ->notNull()
                ->comment('ID автора'),
            'created_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата создания'),
        ]);

        $this->addForeignKey(
            'fk-book_author-book_id',
            self::TABLE_NAME,
            'book_id',
            self::BOOK_TABLE_NAME,
            'id',
            'CASCADE',
        );

        $this->addForeignKey(
            'fk-book_author-author_id',
            self::TABLE_NAME,
            'author_id',
            self::AUTHOR_TABLE_NAME,
            'id',
            'CASCADE',
        );

        $this->createIndex('idx-book_author-unique', self::TABLE_NAME, ['book_id', 'author_id'], true);
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-book_author-book_id', self::TABLE_NAME);
        $this->dropForeignKey('fk-book_author-author_id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
