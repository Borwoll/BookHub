<?php

declare(strict_types=1);

$db = require __DIR__ . '/db.php';

$db['dsn'] .= '_test';

return $db;
