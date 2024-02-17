<?php

namespace App\Model\Work\UseCase\Projects\Role\Copy;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $id;

    #[NotBlank]
    public $name;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}