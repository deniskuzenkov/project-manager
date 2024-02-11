<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\Model\Work\Entity\Members\Member\Member;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public $id;

    #[NotBlank]
    public $group;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromMember(Member $member): self
    {
        $command = new self($member->getId()->getValue());
        $command->group = $member->getGroup()->getId()->getValue();
        return $command;
    }
}