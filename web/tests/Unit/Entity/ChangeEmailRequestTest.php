<?php

namespace App\Tests\Unit\Entity;

use App\Tests\Util\EntityCase;
use App\Entity\ChangeEmailRequest;

class ChangeEmailRequestTest extends EntityCase
{
    private function getValidEntity(): ChangeEmailRequest
    {
        return (new ChangeEmailRequest())
            ->setEmail("johh@doe.fr")
        ;
    }

    public function testNoErrorWhenChangeEmailRequestIsValid(): void
    {
        $this->assertHasErrors($this->getValidEntity(), 0);
    }

    public function testErrorWhenChangeEmailRequestNoEmail(): void
    {
        $changeRequestEmail = $this->getValidEntity()->setEmail("");
        $this->assertHasErrors($changeRequestEmail, 1);
    }

    public function testErrorWhenChangeEmailRequestEmailIsNotValid(): void
    {
        $changeRequestEmail = $this->getValidEntity()->setEmail("bbbbbbbbbbbbbbbbb");
        $this->assertHasErrors($changeRequestEmail, 1);
    }
}
