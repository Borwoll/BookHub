<?php

declare(strict_types=1);

namespace app\domain\User\Events;

use app\domain\Common\DomainEvent;
use app\domain\User\Entities\User;

final class UserCreated extends DomainEvent
{
    public function __construct(
        private readonly User $user,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'user.created';
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->user->getId(),
            'username' => $this->user->getUsername()->getValue(),
            'email' => $this->user->getEmail()->getValue(),
            'role' => $this->user->getRole(),
        ];
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
