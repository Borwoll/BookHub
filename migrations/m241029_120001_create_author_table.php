<?php

declare(strict_types=1);

use yii\db\Migration;

class m241029_120001_create_author_table extends Migration
{
    public const string TABLE_NAME = '{{%author}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this
                ->primaryKey()
                ->comment('ID автора'),
            'full_name' => $this
                ->string()
                ->notNull()
                ->comment('ФИО автора'),
            'created_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата создания'),
            'updated_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата обновления'),
        ]);

        $this->createIndex('idx-author-full_name', self::TABLE_NAME, 'full_name');
    }

    public function safeDown(): void
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
