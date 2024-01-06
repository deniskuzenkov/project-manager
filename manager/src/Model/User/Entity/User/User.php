<?php

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class User
{
    const STATUS_ACTIVE = 'active';
    const STATUS_WAIT = 'wait';
    const STATUS_NEW = 'new';
    private ?Email $email = null;
    private string $passwordHash;
    private Id $id;
    private DateTimeImmutable $date;
    private ?string $confirmToken;
    private ?ResetToken $resetToken = null;
    private string $status;
    private array|ArrayCollection $networks;
    private Role $role;

    private function __construct(Id $id, DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->status = self::STATUS_NEW;
        $this->networks = new ArrayCollection();
        $this->role = Role::user();
    }

    public static function signUpByEmail(
        Id                 $id,
        \DateTimeImmutable $date,
        Email              $email,
        string             $passwordHash,
        string             $token
    ): self
    {
        $user = new self($id, $date);
        $user->email = $email;
        $user->passwordHash = $passwordHash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    public static function signUpByNetwork(Id $id, \DateTimeImmutable $date ,string $network, string $identity): self
    {
        $user = new self($id, $date);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function requestPasswordReset(ResetToken $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }

        $this->resetToken = $token;
    }

    public function getResetToken(): ResetToken
    {
        if (!$this->resetToken) {
            throw new \DomainException('No requested reset token');
        }

        return $this->resetToken;
    }

    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }

    public function passwordReset(DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }

        $this->passwordHash = $hash;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already the same.');
        }

        $this->role = $role;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}