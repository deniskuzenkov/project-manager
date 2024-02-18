<?php

namespace App\ReadModel\Work\Projects\Project\Filter;

use App\Model\Work\Entity\Projects\Project\Status;

class Filter
{
    public $member;
    public ?string $name = null;
    public string $status = Status::ACTIVE;

    private function __construct(?string $member)
    {
        $this->member = $member;
    }

    public static function all(): self
    {
        return new self(null);
    }

    public static function forMember(string $id): self
    {
        return new self($id);
    }
}