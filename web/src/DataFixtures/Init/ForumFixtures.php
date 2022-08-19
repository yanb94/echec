<?php

namespace App\DataFixtures\Init;

use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\DataFixtures\Init\InitFixtures;
use App\DataFixtures\Init\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Init\CategoryFixtures;
use App\Entity\Message;
use App\Entity\Post;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ForumFixtures extends InitFixtures implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $member = $this->userRepository->findOneBy(['username' => 'john']);
        $admin = $this->userRepository->findOneBy(['username' => 'admin']);

        $categories = $this->categoryRepository->findAll();
        $cats = [];

        $fakeMsg = file_get_contents(__DIR__."/../../../fileFixture/msg.txt");

        foreach ($categories as $category) {
            $cats[$category->getName()] = $category;
        }

        for ($i=0; $i < 50; $i++) {
            $t = $i + 1;

            $post = (new Post())
                ->setTitle("Post ".$t)
                ->setAuthor($i%2 ? $member : $admin)
            ;
            
            $post = $this->selectCategory($post, $i, $cats);
            
            $startMsg = (new Message())
                ->setAuthor($post->getAuthor())
                ->setContent($fakeMsg);
            ;
            
            $post->setStartMsg($startMsg);

            for ($b=0; $b < 10; $b++) {
                $msg = (new Message())
                    ->setAuthor($b%2 ? $member : $admin)
                    ->setContent($fakeMsg)
                ;

                $post->addMessage($msg);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function selectCategory(Post $post, int $index, array $cats): Post
    {
        
        if ($index%5 == 0) {
            $post->addCategory($cats['Catégorie 5']);
            return $post;
        }

        if ($index%4 == 0) {
            $post->addCategory($cats['Catégorie 4']);
            return $post;
        }

        if ($index%3 == 0) {
            $post->addCategory($cats['Catégorie 3']);
            return $post;
        }

        if ($index%2 == 0) {
            $post->addCategory($cats['Catégorie 2']);
            return $post;
        }

        $post->addCategory($cats['Catégorie 1']);

        return $post;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }
}
