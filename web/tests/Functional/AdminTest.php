<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    use FixturesTrait;

    private function getUser(string $user): User
    {
        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);
        return $userRepository->findOneBy(["username" => $user]);
    }

    private function testAdminPage(KernelBrowser $client, ?string $user, bool $login = true): void
    {
        $this->loadFixtures([UserFixtures::class]);

        if ($login) {
            $client->loginUser($this->getUser($user));
        }
        $client->request('GET', '/admin');
    }

    public function testAdminSpaceIsAccessibleForAdminUser(): void
    {
        $client = static::createClient();

        $this->testAdminPage($client, "admin");
        $crawler = $client->followRedirect();

        $admin = $crawler->getUri();

        $this->assertStringContainsString("/admin", $admin);
        $this->assertResponseIsSuccessful();
    }

    public function testAdminSpaceIsNotAccessibleForNormalUser(): void
    {
        $client = static::createClient();

        $this->testAdminPage($client, "john");
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminSpaceIsNotAccessibleForAnnonymous(): void
    {
        $client = static::createClient();

        $this->testAdminPage($client, null, false);
        $this->assertResponseRedirects();
    }
}
