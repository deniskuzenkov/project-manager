<?php

namespace App\Model\Work\Entity\Projects;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id extends \App\Model\Work\Entity\Projects\Project\Department\Id
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
       return $this->value;
    }
}