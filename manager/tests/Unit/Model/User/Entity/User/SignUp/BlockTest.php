<?php

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Role;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();
        $user->activate();
        $user->block();
        self::assertTrue($user->isBlocked());
        self::assertFalse($user->isActive());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();
        $user->block();
        $this->expectExceptionMessage('User is already blocked.');
        $user->block();
    }
}