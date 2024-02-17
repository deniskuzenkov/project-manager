<?php

namespace App\Model\Work\Entity\Projects\Role;

use Webmozart\Assert\Assert;

class Permission
{
    public const MANAGE_PROJECT_MEMBERS = 'manager_project_members';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::names());
        $this->name = $name;
    }

    public static function names(): array
    {
        return [
          self::MANAGE_PROJECT_MEMBERS,
        ];
    }
    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}