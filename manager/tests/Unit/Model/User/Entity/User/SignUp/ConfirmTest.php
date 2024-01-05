<?php

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ConfirmTest extends TestCase
{
    public function testSuccess()
    {
        $user = $this->buildSignedUpUser();
        $user->confirmSignUp();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());
        self::assertNull( $user->getConfirmToken());
    }

    public function testAlready()
    {
        $user = $this->buildSignedUpUser();
        $user->confirmSignUp();

        self::expectExceptionMessage('User is already confirmed.');

        $user->confirmSignUp();
    }

    private function buildSignedUpUser(): User
    {
        $user = new User(Id::next(), new \DateTimeImmutable());
        $user->signUpByEmail(
            new Email('test@mail.ru'),
            'hash',
            $token = 'token'
        );
        return $user;
    }
}