<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\UseCase\Projects\Project\Archive as Archive;
use App\Model\Work\UseCase\Projects\Project\Edit as Edit;
use App\Model\Work\UseCase\Projects\Project\Reinstate as Reinstate;
use App\Model\Work\UseCase\Projects\Project\Remove as Remove;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/work/projects/{project_id}/settings", name: "work.projects.project.settings")]
#[IsGranted("ROLE_WORK_MANAGE_PROJECTS")]
class SettingsController extends AbstractController
{
    private LoggerInterface $logger;
    private ProjectRepository $projects;

    public function __construct(LoggerInterface $logger, ProjectRepository $projects)
    {
        $this->logger = $logger;
        $this->projects = $projects;
    }

    #[Route("", name: "", requirements: ['project_id' => Guid::PATTERN])]
    public function show(string $project_id): Response
    {
        $project = $this->projects->get(new Id($project_id));
        return $this->render('app/work/projects/project/settings/show.html.twig', compact('project'));
    }

    #[Route("/edit", name: ".edit")]
    public function edit(string $project_id, Request $request, Edit\Handler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));

        $command = Edit\Command::fromProject($project);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/archive", name: ".archive", methods: ['POST'])]
    public function archive(string $project_id, Request $request, Archive\Handler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));

        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
        }

        $command = new Archive\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
    }

    #[Route("/reinstate", name: ".reinstate", methods: ['POST'])]
    public function reinstate(string $project_id, Request $request, Reinstate\Handler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));

        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
        }

        $command = new Reinstate\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route("/delete", name: ".delete", methods: ['POST'])]
    public function delete(string $project_id, Request $request, Remove\Handler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
        }

        $command = new Remove\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects');
    }
}