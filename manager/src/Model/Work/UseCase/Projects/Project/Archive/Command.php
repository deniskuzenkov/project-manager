<?php

namespace App\Model\Work\UseCase\Projects\Project\Archive;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}