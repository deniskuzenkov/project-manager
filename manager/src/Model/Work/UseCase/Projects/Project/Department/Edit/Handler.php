<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Department\Id;
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
        $project = $this->projects->get(new \App\Model\Work\Entity\Projects\Id($command->project));
        $project->editDepartment(new Id($command->id), $command->name);
        $this->flusher->flush();
    }
}