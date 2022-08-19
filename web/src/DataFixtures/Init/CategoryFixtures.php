<?php

namespace App\DataFixtures\Init;

use App\Entity\Category;
use App\DataFixtures\Init\InitFixtures;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends InitFixtures
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 5; $i++) {
            $t = $i + 1;
            $category = new Category();
            $category->setName("Catégorie ".$t);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
