<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordTest extends WebTestCase
{
    use FixturesTrait;

    public function testPasswordResetRequestIsAccessible(): void
    {
        $client = static::createClient();

        $client->request("GET", '/reset-password');

        $this->assertResponseIsSuccessful();
    }

    public function testPasswordResetRequestIsNotAccessibleWhenLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([UserFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $client->request("GET", '/reset-password');

        $this->assertResponseStatusCodeSame(403);
    }
}
