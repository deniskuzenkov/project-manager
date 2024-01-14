<?php

namespace App\Tests\Functional;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testGuest(): void
    {
       $client = static::createClient();
       $client->request('GET', '/');

       $this->assertSame(302, $client->getResponse()->getStatusCode());
       $this->assertSame('/login', $client->getResponse()->headers->get('location'));
    }

    /**
     * @throws \Exception
     */
    public function testSuccess(): void
    {
        /*$client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->getByEmail(new Email('admin@admin.com'));

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g. the profile page
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello');*/
    }
}