<?php

namespace App\Tests\Functional\Command;

use PHPUnit\Framework\TestCase;
use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserAdminDemoteCommandTest extends KernelTestCase
{
    use FixturesTrait;

    private function testCommand(string $input, string $resultWaited)
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $this->loadFixtures([UserFixtures::class]);

        $command = $application->find('user:admin:demote');
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([$input]);

        $commandTester->execute(['command' => $command->getName()]);
        
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString($resultWaited, $output);
    }

    public function testCommandDemoteTheUser(): void
    {
        $this->testCommand('admin', "'admin' a bien perdu le rang d'administrateur");
    }

    public function testCommandWhenUsernameMissed(): void
    {
        $this->testCommand('', 'Vous devez entrez un nom d\'utilisateur');
    }

    public function testCommandWhenUsernameNotExist(): void
    {
        $this->testCommand('babar', "L'utilisateur 'babar' n'existe pas");
    }

    public function testCommandWhenUserIsNotAdmin(): void
    {
        $this->testCommand('john', "'john' n'est pas administrateur");
    }
}
