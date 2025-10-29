<?php

declare(strict_types=1);

return [
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'admin@example.com',
    'senderEmail' => getenv('SENDER_EMAIL') ?: 'noreply@example.com',
    'senderName' => getenv('SENDER_NAME') ?: 'Example.com mailer',
];
