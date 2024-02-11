<?php

namespace App\Model\Work\UseCase\Members\Member\Reinstate;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
    private MemberRepository $member;
    private Flusher $flusher;

    public function __construct(MemberRepository $member, Flusher $flusher)
    {
        $this->flusher = $flusher;
        $this->member = $member;
    }

    public function handle(Command $command): void
    {
        $member = $this->member->get(new Id($command->id));
        $member->reinstate();
        $this->flusher->flush();
    }
}