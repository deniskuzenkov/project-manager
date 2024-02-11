<?php

namespace App\Model\Work\UseCase\Projects\Project\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
    public ProjectRepository $projects;
    public Flusher $flusher;

    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->flusher = $flusher;
        $this->projects = $projects;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->id));

        $project->edit(
            $command->name,
            $command->sort
        );

        $this->flusher->flush();
    }
}