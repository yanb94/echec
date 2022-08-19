<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ForumFixtures extends Fixture
{
    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager)
    {
        $category1 = (new Category())
            ->setName("Categorie 1")
        ;
        $category2 = (new Category())
            ->setName("Categorie 2")
        ;
        $category3 = (new Category())
            ->setName("Categorie 3")
        ;

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);

        $user = (new User())
            ->setEmail("example@example.com")
            ->setFirstname("John")
            ->setLastname("Doe")
            ->setUsername("john")
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true)
        ;

        $encodedPassword = $this->passwordEncoder->encodePassword($user, "password");
        $user->setPassword($encodedPassword);

        $manager->persist($user);

        for ($i=0; $i < 10; $i++) {
            $post = (new Post())
                ->setTitle("Post ".$i)
                ->addCategory($category1)
                ->setAuthor($user)
            ;

            $msgStart = (new Message())
                ->setAuthor($user)
                ->setContent("Lorem ipsum truc")
                ->setPost($post)
            ;

            $post->setStartMsg($msgStart);

            $msg = (new Message())
                ->setAuthor($user)
                ->setContent("Lorem ipsum truc")
            ;

            $post->addMessage($msg);

            $manager->persist($msg);
            $manager->persist($msgStart);
            $manager->persist($post);

            if ($i == 0) {
                $post = (new Post())
                ->setTitle("Post ".$i."-b")
                ->addCategory($category1)
                ->setAuthor($user)
                ->setIsModerate(true)
                ;

                $msgStart = (new Message())
                    ->setAuthor($user)
                    ->setContent("Lorem ipsum truc")
                    ->setPost($post)
                ;

                $post->setStartMsg($msgStart);

                $msg = (new Message())
                    ->setAuthor($user)
                    ->setContent("Lorem ipsum truc")
                ;

                $post->addMessage($msg);

                $manager->persist($msg);
                $manager->persist($msgStart);
                $manager->persist($post);
            }
        }


        $manager->flush();
    }
}
