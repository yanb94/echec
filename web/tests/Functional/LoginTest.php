<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{

    use FixturesTrait;

    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();

        $crawler = $client->request("GET", '/login');

        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter("input#inputUsername"));
        $this->assertCount(1, $crawler->filter("input#inputPassword"));
        $this->assertCount(1, $crawler->filter(".login-page form button"));

        $this->assertCount(1, $crawler->filter(".login-page a[href='/register']"));
    }

    public function testLoginPageIsNotAcessibleWhenLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([UserFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $client->request("GET", "/login");

        $this->assertResponseStatusCodeSame(403);
    }
}
