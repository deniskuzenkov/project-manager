<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $project;

    #[NotBlank]
    public string $id;

    public function __construct(string $project, string $id)
    {
        $this->project = $project;
        $this->id = $id;
    }
}