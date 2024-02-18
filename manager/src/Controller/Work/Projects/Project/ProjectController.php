<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Security\Voter\Work\ProjectAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/work/projects", name: "work.projects.project")]
class ProjectController extends AbstractController
{
    private ProjectRepository $projects;

    public function __construct(ProjectRepository $projects)
    {
        $this->projects = $projects;
    }

    #[Route("/{id}", name: ".show", requirements: ["id" => Guid::PATTERN])]
    public function show(string $id): Response
    {
        $project = $this->projects->get(new Id($id));
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);
        return $this->render('app/work/projects/project/show.html.twig', compact('project'));
    }
}