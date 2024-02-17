<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Members\Member\Id as MemberId;

class Handler
{
    private ProjectRepository $projects;
    private Flusher $flusher;
    private MemberRepository $members;

    public function __construct(
        ProjectRepository $projects,
        MemberRepository $members,
        Flusher $flusher
    )
    {
        $this->projects = $projects;
        $this->flusher = $flusher;
        $this->members = $members;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->project));
        $member = $this->members->get(new MemberId($command->member));

        $project->removeMember($member->getId());

        $this->flusher->flush();
    }
}