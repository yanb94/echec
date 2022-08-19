<?php

namespace App\DataFixtures\Init;

use App\Entity\Legal;
use App\DataFixtures\Init\InitFixtures;
use Doctrine\Persistence\ObjectManager;

class LegalFixtures extends InitFixtures
{
    public function load(ObjectManager $manager): void
    {
        $legalsData = [
            [
                "title" => "Mention Légale",
                "label" => "Mention Légale"
            ],
            [
                "title" => "Condition générale d'utilisation",
                "label" => "C.G.U"
            ],
            [
                "title" => "Politique de confidentialité",
                "label" => "Confidentialité"
            ]
        ];

        $fakeContent = file_get_contents(__DIR__."/../../../fileFixture/legal.txt");

        foreach ($legalsData as $data) {
            $legal = (new Legal())
                ->setTitle($data['title'])
                ->setTitleLink($data['label'])
                ->setContent($fakeContent);
            ;

            $manager->persist($legal);
        }

        $manager->flush();
    }
}
