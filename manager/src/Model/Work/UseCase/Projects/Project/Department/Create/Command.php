<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Create;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $project;

    #[NotBlank]
    public $name;

    public function __construct(string $project)
    {
        $this->project = $project;
    }
}