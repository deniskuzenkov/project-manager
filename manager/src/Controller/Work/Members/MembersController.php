<?php

declare(strict_types=1);

namespace App\Controller\Work\Members;

use App\Annotation\Guid;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\UseCase\Members\Member\Move\Form;
use App\Model\Work\UseCase\Members\Member\Reinstate\Command;
use App\Model\Work\UseCase\Members\Member\Reinstate\Handler;
use App\ReadModel\Work\Members\Member\Filter;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/work/members", name: "work.members")]
class MembersController extends AbstractController
{
    private const PER_PAGE = 20;

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('', name: '')]
    public function index(Request $request, MemberFetcher $fetcher): Response
    {
        $filter = new Filter\Filter();

        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'asc')
        );

        return $this->render('app/work/members/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/create/{id}", name: ".create")]
    public function create(User $user, Request $request, MemberFetcher $members, \App\Model\Work\UseCase\Members\Member\Create\Handler $handler): Response
    {
        if ($members->exists($user->getId()->getValue())) {
            $this->addFlash('error', 'Member already exists.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new \App\Model\Work\UseCase\Members\Member\Create\Command($user->getId()->getValue());
        $command->firstName = $user->getName()->getFirst();
        $command->lastName = $user->getName()->getLast();
        $command->email = $user->getEmail()?->getValue();

        $form = $this->createForm(\App\Model\Work\UseCase\Members\Member\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.members.show', ['id' => $user->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route("/{id}/edit", name: ".edit")]
    public function edit(Member $member, Request $request, \App\Model\Work\UseCase\Members\Member\Edit\Handler $handler): Response
    {
        $command = \App\Model\Work\UseCase\Members\Member\Edit\Command::fromMember($member);

        $form = $this->createForm(\App\Model\Work\UseCase\Members\Member\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/edit.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}/move", name: ".move")]
    public function move(Member $member, Request $request, \App\Model\Work\UseCase\Members\Member\Move\Handler $handler): Response
    {
        $command = \App\Model\Work\UseCase\Members\Member\Move\Command::fromMember($member);

        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/move.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/archive', name: '.archive', methods: ['POST'])]
    public function archive(Member $member, Request $request, \App\Model\Work\UseCase\Members\Member\Archive\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
        }

        $command = new \App\Model\Work\UseCase\Members\Member\Archive\Command($member->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
    }
    #[Route('/{id}/reinstate', name: '.reinstate', methods: ['POST'])]
    public function reinstate(Member $member, Request $request, Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
        }

        if ($member->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to reinstate yourself.');
            return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
        }

        $command = new Command($member->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
    }

    #[Route('/{id}', name: '.show', requirements: ['id'=> Guid::PATTERN])]
    public function show(Member $member, DepartmentFetcher $fetcher): Response
    {
        $departments = $fetcher->allOfMember($member->getId()->getValue());
        return $this->render('app/work/members/show.html.twig', compact('member', 'departments'));
    }
}