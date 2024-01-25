<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email as UserEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class NewEmailConfirmTokenSender
{
    private MailerInterface $mailer;
    private Environment $twig;
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(UserEmail $email, string $token): void
    {
        $body = $this->twig->render('mail/user/email.html.twig', [
            'token' => $token
        ]);
        $email = (new TemplatedEmail())
            ->from('robot@test.com')
            ->to($email->getValue())
            ->subject('Email Confirmation')
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