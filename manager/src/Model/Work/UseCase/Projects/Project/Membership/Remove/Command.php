<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $project;
    /**
     * @Assert\NotBlank()
     */
    public string $member;

    public function __construct(string $project, string $member)
    {
        $this->project = $project;
        $this->member = $member;
    }
}