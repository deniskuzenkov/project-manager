<?php

namespace App\Model\Work\UseCase\Projects\Project\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\Project;
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
        $project = new Project(
            Id::next(),
            $command->name,
            $command->sort
        );

        $this->projects->add($project);
        $this->flusher->flush();
    }
}