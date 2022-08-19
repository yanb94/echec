<?php

namespace App\Tests\Functional\Command;

use PHPUnit\Framework\TestCase;
use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserAdminPromoteCommandTest extends KernelTestCase
{
    use FixturesTrait;

    private function testCommand(string $input, string $resultWaited)
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $this->loadFixtures([UserFixtures::class]);

        $command = $application->find('user:admin:promote');
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([$input]);

        $commandTester->execute(['command' => $command->getName()]);
        
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString($resultWaited, $output);
    }

    public function testCommandPromoteTheUser(): void
    {
        $this->testCommand('john', "'john' a bien été élevé au rang d'administrateur");
    }

    public function testCommandWhenUsernameMissed(): void
    {
        $this->testCommand('', 'Vous devez entrez un nom d\'utilisateur');
    }

    public function testCommandWhenUsernameNotExist(): void
    {
        $this->testCommand('babar', "L'utilisateur 'babar' n'existe pas");
    }

    public function testCommandWhenUserIsAlreadyAdmin(): void
    {
        $this->testCommand('admin', "'admin' est déjà administrateur");
    }
}
