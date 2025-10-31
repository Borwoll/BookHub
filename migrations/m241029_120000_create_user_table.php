<?php

declare(strict_types=1);

use yii\db\Migration;

final class m241029_120000_create_user_table extends Migration
{
    public const string TABLE_NAME = '{{%user}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this
                ->primaryKey()
                ->comment('ID пользователя'),
            'username' => $this
                ->string()
                ->notNull()
                ->unique()
                ->comment('Имя пользователя'),
            'email' => $this
                ->string()
                ->notNull()
                ->unique()
                ->comment('Электронная почта'),
            'password_hash' => $this
                ->string()
                ->notNull()
                ->comment('Хеш пароля'),
            'phone' => $this
                ->string(20)
                ->comment('Номер телефона для SMS уведомлений'),
            'auth_key' => $this
                ->string(32)
                ->notNull()
                ->comment('Ключ аутентификации'),
            'access_token' => $this
                ->string(32)
                ->defaultValue(null)
                ->comment('Токен доступа'),
            'role' => $this
                ->string(20)
                ->notNull()
                ->defaultValue('user')
                ->comment('Роль (guest или user)'),
            'status' => $this
                ->smallInteger()
                ->notNull()
                ->defaultValue(10)
                ->comment('Статус пользователя'),
            'created_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата создания'),
            'updated_at' => $this
                ->integer()
                ->notNull()
                ->comment('Дата обновления'),
        ]);

        $this->createIndex('idx-user-status', self::TABLE_NAME, 'status');
        $this->createIndex('idx-user-role', self::TABLE_NAME, 'role');
    }

    public function safeDown(): void
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
