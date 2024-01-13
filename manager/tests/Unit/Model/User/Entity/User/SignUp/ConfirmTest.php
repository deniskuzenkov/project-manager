<?php

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());
        self::assertNull( $user->getConfirmToken());
    }

    public function testAlready()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        self::expectExceptionMessage('User is already confirmed.');

        $user->confirmSignUp();
    }


}