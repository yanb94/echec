<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Legal;
use App\Tests\Util\EntityCase;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class LegalTest extends EntityCase
{
    private function getValidEntity(): Legal
    {
        return (new Legal())
            ->setTitle("Mention Légale")
            ->setTitleLink("Mention Légale")
            ->setContent(
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Mauris maximus nibh nulla, vitae mollis mi commodo rhoncus.
                Phasellus non aliquet nisl. In tempus scelerisque cursus.
                Duis vel risus fringilla, commodo leo vel, eleifend mauris."
            )
        ;
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getValidEntity(), 0);
    }

    /// Title

    public function testHasErrorWhenTitleIsEmpty(): void
    {
        $legal = $this->getValidEntity()
            ->setTitle("")
        ;
        $this->assertHasErrors($legal, 2);
    }

    public function testHasErrorWhenTitleIsTooSmall(): void
    {
        $legal = $this->getValidEntity()
            ->setTitle("aa")
        ;
        $this->assertHasErrors($legal, 1);
    }

    public function testHasErrorWhenTitleIsTooBig(): void
    {
        $legal = $this->getValidEntity()
            ->setTitle("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
        ;
        $this->assertHasErrors($legal, 1);
    }

    /// Title Link

    public function testHasErrorWhenTitleLinkIsEmpty(): void
    {
        $legal = $this->getValidEntity()
            ->setTitleLink("")
        ;
        $this->assertHasErrors($legal, 2);
    }

    public function testHasErrorWhenTitleLinkIsTooSmall(): void
    {
        $legal = $this->getValidEntity()
            ->setTitleLink("aa")
        ;
        $this->assertHasErrors($legal, 1);
    }

    public function testHasErrorWhenTitleLinkIsTooBig(): void
    {
        $legal = $this->getValidEntity()
            ->setTitleLink("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
        ;
        $this->assertHasErrors($legal, 1);
    }

    /// Content

    public function testHasErrorWhenContentIsEmpty(): void
    {
        $legal = $this->getValidEntity()
            ->setContent("")
        ;
        $this->assertHasErrors($legal, 2);
    }

    public function testHasErrorWhenContentIsTooSmall(): void
    {
        $legal = $this->getValidEntity()
            ->setContent("aa")
        ;
        $this->assertHasErrors($legal, 1);
    }
}
