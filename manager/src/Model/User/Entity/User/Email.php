<?php

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    private string $value;
    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::email($value);
        $this->value = mb_strtolower($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(Email $email): bool
    {
        return $this->value === $email->value;
    }

}