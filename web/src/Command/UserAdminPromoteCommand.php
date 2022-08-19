<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserAdminPromoteCommand extends Command
{
    protected static $defaultName = 'user:admin:promote';
    protected static $defaultDescription = 'Add admin right to a user with this username';

    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $io->ask("Entrez le nom de l'utilisateur");

        if (!is_null($username)) {

            /** @var User */
            $user = $this->userRepository
                ->findOneBy(['username' => $username])
            ;

            if (!is_null($user)) {
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    $io->info("'".$username."' est déjà administrateur");
                    return Command::FAILURE;
                }
                
                $user->setRoles(['ROLE_ADMIN']);
                
                $this->em->persist($user);
                $this->em->flush();

                $io->success("'".$username."' a bien été élevé au rang d'administrateur");

                return Command::SUCCESS;
            }

            $io->error("L'utilisateur '".$username."' n'existe pas");
            return Command::FAILURE;
        }

        $io->error('Vous devez entrez un nom d\'utilisateur');

        return Command::FAILURE;
    }
}
