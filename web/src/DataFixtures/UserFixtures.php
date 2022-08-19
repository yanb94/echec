<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
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

        $userAdmin = (new User())
            ->setEmail("admin@admin.com")
            ->setFirstname("Admin")
            ->setLastname("Nistrateur")
            ->setUsername("admin")
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
        ;

        $encodedPassword = $this->passwordEncoder->encodePassword($userAdmin, "password");
        $userAdmin->setPassword($encodedPassword);

        $manager->persist($user);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
