<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Tests\Util\EntityCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class UserTest extends EntityCase
{
    use FixturesTrait;

    private function getValidEntity(): User
    {
        return (new User())
            ->setEmail("johh@doe.fr")
            ->setFirstname("john")
            ->setLastname('doe')
            ->setUsername("john")
        ;
    }

    public function testNoErrorWhenUserIsValid(): void
    {
        $this->loadFixtures([]);
        $this->assertHasErrors($this->getValidEntity(), 0);
    }

    /// Email

    public function testErrorWhenNoEmail(): void
    {
        $user = $this->getValidEntity()->setEmail("");
        $this->assertHasErrors($user, 1);
    }

    public function testErrorWhenEmailIsNotValid(): void
    {
        $user = $this->getValidEntity()->setEmail("yqhyqdqudhudhudqhu");
        $this->assertHasErrors($user, 1);
    }

    /// Firstname

    public function testErrorWhenNoFirstname(): void
    {
        $user = $this->getValidEntity()->setFirstname("");
        $this->assertHasErrors($user, 2);
    }

    public function testErrorWhenFirstnameIsTooSmall(): void
    {
        $user = $this->getValidEntity()->setFirstname("a");
        $this->assertHasErrors($user, 1);
    }

    public function testErrorWhenFirstnameIsTooBig(): void
    {
        $user = $this->getValidEntity()->setFirstname("Loremipsumdolorsitametconsecteturadipiscingestaaaaaaaaaaaa");
        $this->assertHasErrors($user, 1);
    }

    public function testErrorWhenFirstnameIsNotValid(): void
    {
        $user = $this->getValidEntity()->setFirstname("johnDoe3");
        $this->assertHasErrors($user, 1);
    }

    /// Lastname

    public function testErrorWhenNoLastname(): void
    {
        $user = $this->getValidEntity()->setLastname("");
        $this->assertHasErrors($user, 2);
    }

    public function testErrorWhenLastnameIsTooSmall(): void
    {
        $user = $this->getValidEntity()->setLastname("a");
        $this->assertHasErrors($user, 1);
    }

    public function testErrorWhenLastnameIsTooBig(): void
    {
        $user = $this->getValidEntity()->setLastname("Loremipsumdolorsitametconsecteturadipiscingestaaaaaaaaaaaa");
        $this->assertHasErrors($user, 1);
    }

    public function testErrorWhenLastnameIsNotValid(): void
    {
        $user = $this->getValidEntity()->setLastname("johnDoe3");
        $this->assertHasErrors($user, 1);
    }

    /// Username

    public function testErrorWhenNoUsername(): void
    {
        $user = $this->getValidEntity()->setUsername("");
        $this->assertHasErrors($user, 2);
    }

    public function testErrorWhenUsernameIsTooSmall(): void
    {
        $user = $this->getValidEntity()->setUsername("a");
        $this->assertHasErrors($user, 1);
    }

    public function testErrorWhenUsernameIsTooBig(): void
    {
        $user = $this->getValidEntity()->setUsername("Loremipsumdolorsitametconsecteturadipiscingestaaaaaaaaaaaa");
        $this->assertHasErrors($user, 1);
    }
}
