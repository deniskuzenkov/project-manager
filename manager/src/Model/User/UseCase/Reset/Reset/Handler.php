<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;


class Handler
{
    private UserRepository $users;
    private Flusher $flusher;
    private PasswordHasher $hasher;


    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Flusher        $flusher
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByResetToken($command->token)) {
            throw  new \DomainException('Incorrect or reset token.');
        }

        $user->passwordReset(
          new \DateTimeImmutable(),
          $this->hasher->hash($command->password)
        );

        $this->flusher->flush();
    }
}