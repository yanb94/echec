<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageTest extends WebTestCase
{
    public function testHomepageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request("GET", '/');

        $this->assertResponseIsSuccessful();
    }
}
