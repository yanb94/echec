<?php

namespace App\Tests\Functional;

use App\DataFixtures\LegalFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LegalTest extends WebTestCase
{
    use FixturesTrait;

    public function testLegalPageIsAccessible(): void
    {
        $client = static::createClient();

        $this->loadFixtures([LegalFixtures::class]);

        $crawler = $client->request('GET', '/');

        $this->assertSelectorTextContains("a.footer--info--actions--item:nth-of-type(3)", "Mention Legal");

        $crawler = $client->clickLink("Mention Legal");

        $this->assertSame("http://localhost/legal/mention-legal", $crawler->getBaseHref());

        $this->assertResponseIsSuccessful();
    }
}
