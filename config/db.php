<?php

declare(strict_types=1);

use yii\db\Connection;

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'bookhub';
$username = getenv('DB_USER') ?: 'bookhub_user';
$password = getenv('DB_PASSWORD') ?: 'bookhub_password';

return [
    'class' => Connection::class,
    'dsn' => "mysql:host={$host};dbname={$dbname}",
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8mb4',
];
