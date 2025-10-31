<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

final class User extends ActiveRecord implements IdentityInterface
{
    public const int STATUS_DELETED = 0;

    public const int STATUS_INACTIVE = 9;

    public const int STATUS_ACTIVE = 10;

    public const string ROLE_GUEST = 'guest';

    public const string ROLE_USER = 'user';

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public static function findByUsername(string $username): ?static
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
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
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],

            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_GUEST, self::ROLE_USER]],

            [['username', 'email'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            [['username', 'email'], 'unique'],
            ['email', 'email'],

            ['phone', 'string', 'max' => 20],
            ['password_hash', 'string', 'max' => 255],
            ['auth_key', 'string', 'max' => 32],
            ['access_token', 'string', 'max' => 32],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'phone' => 'Phone',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken(): void
    {
        $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function isGuest(): bool
    {
        return $this->role === self::ROLE_GUEST;
    }

    public function canManageBooks(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
