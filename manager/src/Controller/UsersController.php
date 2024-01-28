<?php

namespace App\Controller;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Create\Command;
use App\Model\User\UseCase\Create\Form;
use App\Model\User\UseCase\Create\Handler;
use App\ReadModel\User\Filter\Filter;
use App\ReadModel\User\UserFetcher;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/users", name:"users")]
class UsersController extends AbstractController
{
    private const PER_PAGE = 2;
    private UserFetcher $users;
    private LoggerInterface $logger;

    public function __construct(UserFetcher $users, LoggerInterface $logger)
    {
        $this->users = $users;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    #[Route('', '')]
    public function index(Request $request, UserFetcher $userFetcher): Response
    {
        $filter = new Filter();
        $form = $this->createForm(\App\ReadModel\User\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $userFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/users/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', '.show')]
    public function show(User $user): Response
    {
        return $this->render('app/users/show.html.twig', compact('user'));
    }

    #[Route('/create', '.create', priority: 2)]
    public function create(Request $request, Handler $handler): Response
    {
        $command = new Command();


        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
               $handler->handle($command);
               return $this->redirectToRoute('users');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/create.html.twig', [
           'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', '.edit')]
    public function edit(User $user, Request $request, \App\Model\User\UseCase\Edit\Handler $handler): Response
    {
        $command = \App\Model\User\UseCase\Edit\Command::fromUser($user);

        $form = $this->createForm(\App\Model\User\UseCase\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('app/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/role', '.role')]
    public function role(User $user, Request $request, \App\Model\User\UseCase\Role\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to change role for yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = \App\Model\User\UseCase\Role\Command::fromUser($user);

        $form = $this->createForm(\App\Model\User\UseCase\Role\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('app/users/role.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/confirm', '.confirm', methods: ['POST'])]
    public function confirm(User $user, Request $request, \App\Model\User\UseCase\SignUp\Confirm\Manual\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('confirm', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new \App\Model\User\UseCase\SignUp\Confirm\Manual\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }
    #[Route('/{id}/activate','.activate', methods: ['POST'])]
    public function activate(User $user, Request $request, \App\Model\User\UseCase\Activate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('activate', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new \App\Model\User\UseCase\Activate\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }


    #[Route('/{id}/block','.block', methods: ['POST'])]
    public function block(User $user, Request $request, \App\Model\User\UseCase\Block\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to block yourself');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }
        if (!$this->isCsrfTokenValid('block', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new \App\Model\User\UseCase\Block\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

}