<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
    private ProjectRepository $projects;
    private Flusher $flusher;
    public function __construct(ProjectRepository $projects, FLusher $flusher)
    {
        $this->flusher = $flusher;
        $this->projects = $projects;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->project));
        $project->addDepartment(
            \App\Model\Work\Entity\Projects\Project\Department\Id::next(),
            $command->name
        );

        $this->flusher->flush();
    }
}