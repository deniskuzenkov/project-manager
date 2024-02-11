<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
    private MemberRepository $members;
    private GroupRepository $groups;
    private Flusher $flusher;

    public function __construct(MemberRepository $members, GroupRepository $groups, Flusher $flusher)
    {
        $this->members = $members;
        $this->groups = $groups;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $member = $this->members->get(new Id($command->id));
        $group = $this->groups->get(new \App\Model\Work\Entity\Members\Group\Id($command->group));

        $member->move($group);
        $this->flusher->flush();
    }
}