<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\UseCase\Projects\Project\Create;
use App\ReadModel\Work\Projects\Project\Filter;
use App\ReadModel\Work\Projects\Project\ProjectFetcher;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/work/projects", name: "work.projects")]
class ProjectsController extends AbstractController
{
    private const PER_PAGE = 50;

    private LoggerInterface $logger;
    private ProjectRepository $projects;

    public function __construct(LoggerInterface $logger, ProjectRepository $projects)
    {
        $this->logger = $logger;
        $this->projects = $projects;
    }

    #[Route("", name: "")]
    public function index(Request $request, ProjectFetcher $fetcher): Response
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

        return $this->render('app/work/projects/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/create", name: ".create")]
    public function create(Request $request, ProjectFetcher $projects, Create\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted('ROLE_WORK_MANAGE_PROJECTS');

        $command = new Create\Command();
        $command->sort = $projects->getMaxSort() + 1;

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects');
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}