<?php

namespace App\Model\Work\UseCase\Members\Member\Create;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $id;

    #[NotBlank]
    public $group;

    #[NotBlank]
    public $firstName;

    #[NotBlank]
    public $lastName;

    #[Email]
    public $email;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

}