<?php

namespace App\Model\User\Service;

use Ramsey\Uuid\Uuid;

class PasswordGenerator
{
    public function generate(): string
    {
        return md5(time());
        return Uuid::uuid4()->toString();
    }
}