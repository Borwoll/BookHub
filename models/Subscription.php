<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

final class Subscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%subscription}}';
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
            [['phone', 'author_id'], 'required'],
            [['author_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => true],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^\+?[1-9]\d{1,14}$/', 'message' => 'Неверный формат номера телефона'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['phone', 'author_id'], 'unique', 'targetAttribute' => ['phone', 'author_id'], 'message' => 'Подписка на этого автора уже существует для данного номера'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'phone' => 'Номер телефона',
            'author_id' => 'Автор',
            'is_active' => 'Активна',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}
