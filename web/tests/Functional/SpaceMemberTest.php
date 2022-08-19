<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SpaceMemberTest extends WebTestCase
{
    use FixturesTrait;

    private function getTestUser(): User
    {
        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);
        return $userRepository->findOneBy(["username" => "john"]);
    }

    private function testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot(string $url):void
    {
        $client = static::createClient();

        $this->loadFixtures([UserFixtures::class]);

        $client->request("GET", $url);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $client->loginUser($this->getTestUser());

        $this->assertResponseIsSuccessful();
    }

    public function testSpaceMemberIsAccessible(): void
    {
        $this->testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot("/space-member");
    }

    public function testEditMemberInfoIsAccessible(): void
    {
        $this->testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot(
            "/space-member/edit-member"
        );
    }

    public function testEditPasswordMemberIsAccessible(): void
    {
        $this->testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot(
            "/space-member/edit-password"
        );
    }

    public function testChangeEmailMemberIsAccessible(): void
    {
        $this->testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot(
            "/space-member/change-email"
        );
    }

    public function testListFollowSubjectIsAccessible(): void
    {
        $this->testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot(
            "/space-member/list-follow-subject"
        );
    }

    public function testMyContributionIsAccessible(): void
    {
        $this->testPageIsAccesibleWhenConnectedAndNotAccessibleIfNot(
            "/space-member/my-contribution"
        );
    }
}
