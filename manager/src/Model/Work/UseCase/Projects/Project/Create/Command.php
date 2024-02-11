<?php

namespace App\Model\Work\UseCase\Projects\Project\Create;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public $name;

    #[NotBlank]
    public $sort;
}