<?php

namespace App\Security;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\User\UserFetcher;
use Doctrine\DBAL\Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{


    public function __construct(private readonly UserFetcher $users)
    {
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface|UserIdentity
    {
       if (!$user instanceof UserIdentity) {
           throw new UnsupportedUserException('Invalid user class ' . \get_class($user));
       }
       return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return UserIdentity::class === $class || is_subclass_of($class, UserIdentity::class);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
       $user = $this->users->findForAuth($identifier);

       if (!$user) {
           throw new UserNotFoundException('User not found');
       }

       return new UserIdentity(
           $user->id,
           $user->email,
           $user->password_hash,
           $user->role,
           $user->status
       );
    }
}