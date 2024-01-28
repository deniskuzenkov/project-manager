<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;


class Handler
{
    private UserRepository $users;
    private PasswordHasher $hasher;
    private PasswordGenerator $generator;
    private Flusher $flusher;

    public function __construct(
        UserRepository     $users,
        PasswordHasher     $hasher,
        PasswordGenerator   $generator,
        Flusher            $flusher
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->generator = $generator;
        $this->flusher = $flusher;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new  \DomainException('User already exists');
        }

        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($this->generator->generate()),
        );

        $this->users->add($user);
        $this->flusher->flush();
    }
}