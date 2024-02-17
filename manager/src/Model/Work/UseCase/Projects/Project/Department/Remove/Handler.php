<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
    private ProjectRepository $projects;
    private Flusher $flusher;

    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->project));
        $project->removeDepartment(new \App\Model\Work\Entity\Projects\Project\Department\Id($command->id));
        $this->flusher->flush();
    }
}