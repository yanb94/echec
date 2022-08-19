<?php

namespace App\Tests\Functional;

use App\DataFixtures\ForumFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Component\Panther\PantherTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumTest extends WebTestCase
{
    use FixturesTrait;

    public function testForumIsAccessibleWhenNotLogged(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forum');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.forum--cont--actions--cont-add--btn');
    }

    public function testForumIsAccessibleWhenLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([UserFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/forum');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.forum--cont--actions--cont-add--btn');
    }

    public function testListOfPostWaited(): void
    {
        $client = static::createClient();

        $this->loadFixtures([ForumFixtures::class]);

        $crawler = $client->request('GET', '/forum');

        $nbPost = $crawler->filter(".forum--post")->count();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.forum--post--infos--title--overflow', "Post 0");
        $this->assertSelectorTextContains('.forum--post--nb-comment--nb', 1);
        $this->assertSelectorTextContains('.forum--post--infos--extra', "De john");

        $this->assertSame(10, $nbPost);
    }

    public function testNotAccessibleAddPostWhenNotLogged(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forum/add-post');

        $crawler = $client->followRedirect();

        $uri = $crawler->getUri();

        $this->assertStringContainsString("/login", $uri);
    }

    public function testAccessibleAddPostWhenLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([ForumFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/forum/add-post');

        $nbCategoryInputs = $crawler->filter('.form-category-checkbox')->count();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists("#post_title");
        $this->assertSame(3, $nbCategoryInputs);
        $this->assertSelectorExists("#post_startMsg_content");
        $this->assertSelectorExists("#post_isRequestAnswer");
        $this->assertSelectorExists(".add-post--cont--card--btn");
    }

    public function testSubjectPageIsAccessibleWhenNotLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([ForumFixtures::class]);

        $crawler = $client->request('GET', '/forum/post/post-0');

        $nbMessages = $crawler->filter('.post-forum-msg')->count();

        $this->assertResponseIsSuccessful();
        $this->assertSame(2, $nbMessages);
        $this->assertSelectorExists('.post-forum--cont--container--not-logged');
        $this->assertSelectorExists('.post-forum--cont--container--not-logged--btn');
    }

    public function testSubjectPageIsAccessibleWhenLogged(): void
    {
        $client = static::createClient();

        $this->loadFixtures([ForumFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/forum/post/post-0');

        $nbMessages = $crawler->filter('.post-forum-msg')->count();

        $this->assertResponseIsSuccessful();
        
        $this->assertSame(2, $nbMessages);
        $this->assertSelectorTextContains('.post-forum-msg--author', 'De john');
        $this->assertSelectorTextContains('.post-forum-msg--content', 'Lorem ipsum truc');
        $this->assertSelectorExists('.post-forum-msg--createdAt');

        $this->assertSelectorExists('.post-forum--cont--container--follow-cont--cont');
        $this->assertSelectorExists('.post-forum--cont--container--form-label');
        $this->assertSelectorExists("#message_content");
        $this->assertSelectorExists('.post-forum--cont--container--btn');
    }

    public function testSubjectIsNotAccessibleWhenModerate(): void
    {
        $client = static::createClient();

        $this->loadFixtures([ForumFixtures::class]);

        /** @var UserRepository */
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(["username" => "john"]);

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/forum/post/post-0-b');

        $this->assertResponseStatusCodeSame(404);
    }
}
