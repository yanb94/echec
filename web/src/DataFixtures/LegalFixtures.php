<?php

namespace App\DataFixtures;

use App\Entity\Legal;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class LegalFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $legal = (new Legal())
            ->setTitle("Mention Legal")
            ->setTitleLink("Mention Legal")
            ->setContent("
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Etiam eleifend arcu magna, eu faucibus ipsum maximus nec.
                Nam non laoreet dolor, quis congue massa. Cras id ultricies erat.
                Nam venenatis volutpat nunc, at aliquet sapien tincidunt eget.
                Aliquam et magna sed lacus eleifend eleifend et quis massa. Nulla facilisi. 
                Praesent malesuada leo eu turpis fermentum, at accumsan ante mattis. 
                Aliquam nisi dui, rhoncus a tempor ornare, luctus eu justo. 
            ");
        
        $manager->persist($legal);

        $manager->flush();
    }
}
