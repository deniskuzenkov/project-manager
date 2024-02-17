<?php

namespace App\Model\Work\UseCase\Projects\Role\Create;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public $name;
    public $permissions;

}