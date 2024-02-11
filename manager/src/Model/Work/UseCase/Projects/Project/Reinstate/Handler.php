<?php

namespace App\Model\Work\UseCase\Projects\Project\Reinstate;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
    private ProjectRepository $projects;
    private Flusher $flusher;
    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->flusher = $flusher;
        $this->projects = $projects;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->id));
        $project->reinstate();
        $this->flusher->flush();
    }
}