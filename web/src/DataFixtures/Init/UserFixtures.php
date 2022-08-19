<?php

namespace App\DataFixtures\Init;

use App\Entity\User;
use App\DataFixtures\Init\InitFixtures;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends InitFixtures
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
        
        $encodedPassword = $this->passwordEncoder->encodePassword($user, "P8ssword?");
        $user->setPassword($encodedPassword);

        $userAdmin = (new User())
            ->setEmail("admin@admin.com")
            ->setFirstname("Admin")
            ->setLastname("Nistrateur")
            ->setUsername("admin")
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
        ;

        $encodedPassword = $this->passwordEncoder->encodePassword($userAdmin, "P8ssword?");
        $userAdmin->setPassword($encodedPassword);

        $manager->persist($user);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
