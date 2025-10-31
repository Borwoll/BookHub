<?php

declare(strict_types=1);

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$aliases = require __DIR__ . '/aliases.php';

$config = [
    'id' => 'basic',
    'name' => 'BookHub',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'book/index',
    'on beforeAction' => function () {
        $registerDi = require __DIR__ . '/di.php';
        $registerDi();
    },
    'aliases' => $aliases,
    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'your-secret-key-here',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'book/index',
                'books' => 'book/index',
                'books/<id:\d+>' => 'book/view',
                'books/create' => 'book/create',
                'books/<id:\d+>/update' => 'book/update',
                'books/<id:\d+>/delete' => 'book/delete',

                'authors' => 'author/index',
                'authors/<id:\d+>' => 'author/view',
                'authors/create' => 'author/create',
                'authors/<id:\d+>/update' => 'author/update',
                'authors/<id:\d+>/delete' => 'author/delete',
                'authors/<id:\d+>/subscribe' => 'author/subscribe',

                'subscriptions' => 'subscription/index',
                'subscriptions/create' => 'subscription/create',
                'subscriptions/view' => 'subscription/view',
                'subscriptions/<id:\d+>/unsubscribe' => 'subscription/unsubscribe',

                'reports' => 'report/index',
                'reports/author' => 'report/author',
                'reports/json' => 'report/json',
            ],
        ],
    ],
    'params' => $params,
];

if (true === YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
