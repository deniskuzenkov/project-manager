<?php

namespace App\Controller\Work\Members;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\UseCase\Members\Group\Create\Command;
use App\Model\Work\UseCase\Members\Group\Create\Form;
use App\Model\Work\UseCase\Members\Group\Create\Handler;
use App\ReadModel\Work\Members\GroupFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/members/groups', name:'work.members.groups')]
#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
clasS GroupController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('', name: '')]
    public function index(GroupFetcher $fetcher): Response
    {
        $groups = $fetcher->all();
        return $this->render('app/work/members/group/index.html.twig', [
            'groups' => $groups
        ]);
    }
    #[Route('/create', name: '.create')]

    public function create(Request $request, Handler $handler): Response
    {
        $command = new Command();

        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $handler->handle($command);
                return $this->redirectToRoute('work.members.groups');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/group/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/{id}/edit", name: ".edit")]
    public function edit(Group $group, Request $request, \App\Model\Work\UseCase\Members\Group\Edit\Handler $handler): Response
    {
        $command = \App\Model\Work\UseCase\Members\Group\Edit\Command::fromGroup($group);

        $form = $this->createForm(\App\Model\Work\UseCase\Members\Group\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}/delete", name: ".delete", methods: ['POST'])]
    public function delete(Group $group, Request $request, \App\Model\Work\UseCase\Members\Group\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
        }

        $command = new \App\Model\Work\UseCase\Members\Group\Remove\Command($group->getId()->getValue());

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.members.groups');
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
    }

    #[Route('/{id}', name: '.show')]
    public function show(): Response
    {
        return $this->redirectToRoute('work.members.groups');
    }
}