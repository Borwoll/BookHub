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

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword(string $attribute, mixed $params = null): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if ($user === null || $user->validatePassword($this->password) === false) {
            $this->addError($attribute, 'Incorrect username or password.');
        }
    }

    public function login(): bool
    {
        if ($this->validate() === false) {
            return false;
        }

        return Yii::$app->user->login(
            $this->getUser(),
            $this->rememberMe ? 3600 * 24 * 30 : 0,
        );
    }

    private function getUser(): ?User
    {
        if ($this->_user !== null) {
            return $this->_user;
        }

        $this->_user = User::findByUsername($this->username);

        return $this->_user;
    }
}
