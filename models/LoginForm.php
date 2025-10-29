<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;

    public $password;

    public $rememberMe = true;

    private $_user = false;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword(string $attribute, ?array $params): void
    {
        if (false === $this->hasErrors()) {
            $user = $this->getUser();

            if (null === $user || false === $user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login(): bool
    {
        if (true === $this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    public function getUser(): ?User
    {
        if (false === $this->_user) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
