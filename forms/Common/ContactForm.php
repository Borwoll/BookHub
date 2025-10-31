<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class ContactForm extends Model
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $body = '';

    public string $verifyCode = '';

    public function rules(): array
    {
        return [
            [['name', 'email', 'subject', 'body'], 'required'],
            ['email', 'email'],
            ['verifyCode', 'captcha'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    public function contact(string $email): bool
    {
        if ($this->validate() === true) {
            Yii::$app
                ->mailer
                ->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }

        return false;
    }
}
