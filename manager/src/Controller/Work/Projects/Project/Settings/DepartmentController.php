<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\UseCase\Projects\Project\Department\Create\Command as CreateCommand;
use App\Model\Work\UseCase\Projects\Project\Department\Create\Form as CreateForm;
use App\Model\Work\UseCase\Projects\Project\Department\Edit\Form as EditForm;
use App\Model\Work\UseCase\Projects\Project\Department\Edit\Command as EditCommand;
use App\Model\Work\UseCase\Projects\Project\Department\Remove\Command as RemoveCommand;
use App\Model\Work\UseCase\Projects\Project\Department\Create\Handler as CreateHandler;
use App\Model\Work\UseCase\Projects\Project\Department\Edit\Handler as EditHandler;
use App\Model\Work\UseCase\Projects\Project\Department\Remove\Handler as RemoveHandler;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("work/projects/{project_id}/settings/departments", name: "work.projects.project.settings.departments")]
#[IsGranted("ROLE_WORK_MANAGE_PROJECTS")]
class DepartmentController extends AbstractController
{
    private LoggerInterface $logger;
    private ProjectRepository $projects;

    public function __construct(LoggerInterface $logger, ProjectRepository $projects)
    {
        $this->logger = $logger;
        $this->projects = $projects;
    }

    #[Route("", name: "")]
    public function index(string $project_id): Response
    {
        $project = $this->projects->get(new Id($project_id));
        return $this->render('app/work/projects/project/settings/departments/index.html.twig', [
            'project' => $project,
            'departments' => $project->getDepartments()
        ]);
    }

    #[Route('/create', name: '.create')]
    public function create(string $project_id, Request $request, CreateHandler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));
        $command = new CreateCommand($project->getId()->getValue());

        $form = $this->createForm(CreateForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/departments/create.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}/edit", name: ".edit")]
    public function edit(string $project_id, string $id, Request $request, EditHandler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));
        $department = $project->getDepartment(new Id($id));

        $command = EditCommand::fromDepartment($project, $department);

        $form = $this->createForm(EditForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.settings.departments.show', ['project_id' => $project->getId(), 'id' => $id]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/departments/edit.html.twig', [
            'project' => $project,
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"POST"})
     * @param Project $project
     * @param string $id
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    #[Route("/{id}/delete", name: ".delete", methods: ['POST'])]
    public function delete(string $project_id, string $id, Request $request, RemoveHandler $handler): Response
    {
        $project = $this->projects->get(new Id($project_id));

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
        }

        $department = $project->getDepartment(new \App\Model\Work\Entity\Projects\Project\Department\Id($id));

        $command = new RemoveCommand($project->getId()->getValue(), $department->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
    }

    #[Route('/{id}/show', name: ".show")]
    public function show(string $project_id): Response
    {
        $project = $this->projects->get(new Id($project_id));
        return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
    }
}