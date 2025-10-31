<?php

declare(strict_types=1);

namespace app\domain\User\Entities;

use app\domain\Common\Entity;
use app\domain\User\Events\UserCreated;
use app\domain\User\Events\UserUpdated;
use app\domain\User\ValueObjects\Email;
use app\domain\User\ValueObjects\Username;

final class User extends Entity
{
    public const STATUS_ACTIVE = 10;

    public const STATUS_INACTIVE = 0;

    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    private array $domainEvents = [];

    public function __construct(
        private Username $username,
        private Email $email,
        private string $passwordHash,
        private string $role = self::ROLE_USER,
        private int $status = self::STATUS_ACTIVE,
        private ?string $phone = null,
        private ?string $authKey = null,
        private ?string $accessToken = null,
        ?int $id = null,
    ) {
        if ($id) {
            $this->setId($id);
        }
    }

    public static function create(
        Username $username,
        Email $email,
        string $passwordHash,
        ?string $phone = null,
    ): self {
        $user = new self($username, $email, $passwordHash, self::ROLE_USER, self::STATUS_ACTIVE, $phone);
        $user->addDomainEvent(new UserCreated($user));

        return $user;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
        $this->addDomainEvent(new UserUpdated($this->getId(), 'email_updated'));
    }

    public function updatePhone(?string $phone): void
    {
        $this->phone = $phone;
        $this->addDomainEvent(new UserUpdated($this->getId(), 'phone_updated'));
    }

    public function changePassword(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
        $this->addDomainEvent(new UserUpdated($this->getId(), 'password_changed'));
    }

    public function setAuthKey(string $authKey): void
    {
        $this->authKey = $authKey;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
        $this->addDomainEvent(new UserUpdated($this->getId(), 'activated'));
    }

    public function deactivate(): void
    {
        $this->status = self::STATUS_INACTIVE;
        $this->addDomainEvent(new UserUpdated($this->getId(), 'deactivated'));
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->username->getValue(),
            'email' => $this->email->getValue(),
            'role' => $this->role,
            'status' => $this->status,
            'phone' => $this->phone,
        ];
    }

    private function addDomainEvent($event): void
    {
        $this->domainEvents[] = $event;
    }
}
