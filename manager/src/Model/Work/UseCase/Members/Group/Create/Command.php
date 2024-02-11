<?php

namespace App\Model\Work\UseCase\Members\Group\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $name;
}