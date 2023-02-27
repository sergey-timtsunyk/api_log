<?php

declare(strict_types=1);

namespace App\Tests\Command;

use JoliCode\Elastically\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateIndexCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $container = self::$kernel->getContainer();

        $bag = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->set(Client::class, $bag);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->set(LoggerInterface::class, $logger);

        $command = $application->find('app:creat-index');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }
}
