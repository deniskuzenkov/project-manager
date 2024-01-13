<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email as UserEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ConfirmTokenSender
{
    private MailerInterface $mailer;
    private Environment $twig;
    private array $from;
    public function __construct(MailerInterface $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function send(UserEmail $email, string $token): void
    {
        $body = $this->twig->render('mail/user/signup.html.twig', [
            'token' => $token
        ]);
        $email = (new TemplatedEmail())
            ->from(...$this->from)
            ->to($email->getValue())
            ->subject('Confirm token')
            ->html($body);
      /*  $email = (new Email())
            ->from(...$this->from)
            ->to($email->getValue())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');*/
       // $transport = Transport::fromDsn('smtp://mailer:1025');
       // $mailer = new Mailer($transport);
        $this->mailer->send($email);

    }
}