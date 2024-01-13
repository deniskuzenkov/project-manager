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
        $user = User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            $network = 'vk',
            $identity = '1234566'
        );


        self::assertTrue($user->isActive());
        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = $networks[0]);
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());
        self::assertTrue($user->getRole()->isUser());

    }

    public function testAlready(): void
    {
        /*$user = (new UserBuilder())->viaNetwork('vk', '0001')->build();

        self::expectExceptionMessage('Network is already attached.');
        User::signUpByNetwork(
            $id,
            $date,
            $network,
            $identity
        );*/

    }
}