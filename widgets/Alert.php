<?php

declare(strict_types=1);

namespace app\widgets;

use Yii;
use yii\bootstrap5\Alert as Bootstrap5Alert;
use yii\bootstrap5\Widget as Bootstrap5Widget;

class Alert extends Bootstrap5Widget
{
    public $alertTypes = [
        'error' => 'alert-danger',
        'danger' => 'alert-danger',
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning',
    ];

    public $closeButton = [];

    public function run(): void
    {
        $session = Yii::$app->session;
        $appendClass = true === isset($this->options['class'])
            ? ' ' . $this->options['class']
            : '';

        foreach (array_keys($this->alertTypes) as $type) {
            $flash = $session->getFlash($type);

            foreach ((array) $flash as $i => $message) {
                echo Bootstrap5Alert::widget([
                    'body' => $message,
                    'closeButton' => $this->closeButton,
                    'options' => array_merge($this->options, [
                        'id' => $this->getId() . '-' . $type . '-' . $i,
                        'class' => $this->alertTypes[$type] . $appendClass,
                    ]),
                ]);
            }

            $session->removeFlash($type);
        }
    }
}
