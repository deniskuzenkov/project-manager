<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Edit;

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
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        Flusher        $flusher
    )
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $user->edit(
            new Email($command->email),
            new Name(
                $command->firstName,
                $command->lastName
            )
        );
        $this->flusher->flush();
    }
}