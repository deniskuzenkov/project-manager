<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Email as UserEmail;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class ResetTokenSender
{
    private $mailer;
    private $twig;
    private $from;
    public function __construct(MailerInterface $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function send(UserEmail $email, string $token): void
    {
       $email = (new TemplatedEmail())
           ->from(...$this->from)
           ->to($email->getValue())
           ->subject('Reset token')
           ->html($this->twig->render('mail/user/reset.html.twig', [
               'token' => $token
           ]));
      /*  $email = (new \Symfony\Component\Mime\Email())
            ->from(...$this->from)
            ->to($email->getValue())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->tem
            ->html('<p>See Twig integration for better HTML integration!</p>');*/

        $this->mailer->send($email);

    }
}