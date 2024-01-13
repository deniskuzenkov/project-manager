<?php

namespace App\Controller\Auth;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\UseCase\Reset\Request\Command as RequestCommand;
use App\Model\User\UseCase\Reset\Reset\Command as ResetCommand;
use App\Model\User\UseCase\Reset\Request\Form as RequestForm;
use App\Model\User\UseCase\Reset\Reset\Form as ResetForm;
use App\Model\User\UseCase\Reset\Request\Handler;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/reset', name: 'auth.reset')]
    public function request(Request $request, Handler $handler): Response
    {
        $command = new RequestCommand();
        $form = $this->createForm(RequestForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());

            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/reset/{token}', name: "auth.reset.reset")]
    public function reset(string $token, Request $request, \App\Model\User\UseCase\Reset\Reset\Handler $handler, UserRepository $users): Response
    {
        if (!$users->existsByResetToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $this->redirectToRoute('home');
        }
        $command = new ResetCommand($token);
        $form = $this->createForm(ResetForm::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
           'form' => $form->createView()
        ]);
    }

}