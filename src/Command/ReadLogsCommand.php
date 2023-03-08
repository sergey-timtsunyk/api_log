<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Logs\ReadLogs;
use App\Log\Handler\LogRow\Message;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:read-logs',
    description: 'Read and save logs file.',
)]
final class ReadLogsCommand extends Command implements SignalableCommandInterface
{
    public function __construct(
        private readonly ContainerBagInterface $params,
        private readonly MessageBusInterface $bus,
        private readonly ReadLogs $readLogs
    ) {
        parent::__construct();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start read log file.');

        $this->readLogs->open($this->params);
        $this->readLogs->read(function ($value) {
            $this->bus->dispatch(new Message($value));
        });

        $output->writeln('Finish read log file.');

        return Command::SUCCESS;
    }

    public function getSubscribedSignals(): array
    {
        return [\SIGINT, \SIGTSTP, \SIGTERM, \SIGQUIT, \SIGSEGV];
    }

    public function handleSignal(int $signal): void
    {
        $this->readLogs->close();
    }
}
