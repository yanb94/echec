<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    use FixturesTrait;

    public function testRegisterPageIsAccessible(): void
    {
        $client = static::createClient();

        $crawler = $client->request("GET", '/register');

        $this->assertCount(8, $crawler->filter(".register-page form input"));
        $this->assertCount(1, $crawler->filter(".register-page form button[type='submit']"));

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterPageIsNotAcessibleWhenLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([UserFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $client->request("GET", "/register");

        $this->assertResponseStatusCodeSame(403);
    }
}
