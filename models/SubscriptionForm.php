<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

final class SubscriptionForm extends Model
{
    public ?int $author_id = null;

    public string $phone = '';

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            ['author_id', 'integer'],
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+?[1-9]\d{1,14}$/'],
        ];
    }
}
