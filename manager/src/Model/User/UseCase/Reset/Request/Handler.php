<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;


class Handler
{
    private UserRepository $users;
    private Flusher $flusher;
    private ResetTokenizer $tokenizer;
    private ResetTokenSender $sender;

    public function __construct(
        UserRepository   $users,
        ResetTokenizer   $tokenizer,
        ResetTokenSender $sender,
        Flusher          $flusher
    )
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));
        $user->requestPasswordReset(
            $this->tokenizer->generatre(),
            new \DateTimeImmutable()
        );

        $this->flusher->flush();
        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}