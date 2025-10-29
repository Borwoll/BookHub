<?php

// Database configuration for Docker environment
// In Docker, use service name 'db' as host
// For local development without Docker, use 'localhost'
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'bookhub';
$username = getenv('DB_USER') ?: 'bookhub_user';
$password = getenv('DB_PASSWORD') ?: 'bookhub_password';

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host={$host};dbname={$dbname}",
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
