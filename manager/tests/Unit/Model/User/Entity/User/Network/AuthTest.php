<?php

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'vk',
            $identity = '1234566'
        );

        self::assertTrue($user->isActive());
        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = $networks[0]);
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());

    }

    public function testAlready(): void
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'vk',
            $identity = '1234566'
        );

        self::expectExceptionMessage('User already signed up.');
        $user->signUpByNetwork($network, $identity);
    }
}