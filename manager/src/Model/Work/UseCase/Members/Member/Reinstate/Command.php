<?php

namespace App\Model\Work\UseCase\Members\Member\Reinstate;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}