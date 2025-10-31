<?php

declare(strict_types=1);

use yii\db\Migration;

class m241029_120004_create_subscription_table extends Migration
{
    public const string TABLE_NAME = '{{%subscription}}';

    public const string AUTHOR_TABLE_NAME = '{{%author}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this
                ->primaryKey()
                ->comment('ID подписки'),
            'phone' => $this
                ->string(20)
                ->notNull()
                ->comment('Номер телефона для SMS уведомлений'),
            'author_id' => $this
                ->integer()
                ->notNull()
                ->comment('ID автора'),
            'is_active' => $this
                ->boolean()
                ->defaultValue(true)
                ->comment('Активна ли подписка'),
            'created_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата создания'),
            'updated_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата обновления'),
        ]);

        $this->addForeignKey(
            'fk-subscription-author_id',
            self::TABLE_NAME,
            'author_id',
            self::AUTHOR_TABLE_NAME,
            'id',
            'CASCADE',
        );

        $this->createIndex('idx-subscription-phone', self::TABLE_NAME, 'phone');
        $this->createIndex('idx-subscription-author_id', self::TABLE_NAME, 'author_id');
        $this->createIndex('idx-subscription-active', self::TABLE_NAME, 'is_active');
        $this->createIndex('idx-subscription-phone-author', self::TABLE_NAME, ['phone', 'author_id'], true);
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-subscription-author_id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
