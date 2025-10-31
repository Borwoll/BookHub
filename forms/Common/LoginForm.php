<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

final class LoginForm extends Model
{
    public string $username = '';

    public string $password = '';

    public bool $rememberMe = true;

    private null|false|User $_user = false;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    public function validatePassword(string $attribute, array $params): void
    {
        if ($this->hasErrors() === false) {
            $user = $this->getUser();

            if ($user === null || $user->validatePassword($this->password) === false) {
                $this->addError($attribute, 'Неверное имя пользователя или пароль.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate() === true) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    public function getUser(): ?User
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
