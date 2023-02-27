<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Component\Logs\ReadLogs;
use App\Component\Logs\ReadLogsFile;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ReadLogsCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $container = self::$kernel->getContainer();

        $bag = $this->getMockBuilder(ContainerBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->set(ContainerBagInterface::class, $bag);

        $bus = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->set(MessageBusInterface::class, $bus);

        $bag = $this->getMockBuilder(ReadLogs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->set(ReadLogs::class, $bag);

        $command = $application->find('app:read-log');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }
}
