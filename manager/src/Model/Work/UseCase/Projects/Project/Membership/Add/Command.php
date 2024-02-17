<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $project;

    #[NotBlank]
    public $member;

    #[NotBlank]
    public $departments;

    #[NotBlank]
    public $roles;

    public function __construct(string $project)
    {
        $this->project = $project;
    }

}