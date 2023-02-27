<?php

declare(strict_types=1);

namespace App\Command;

use App\Log\Model\Log;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JoliCode\Elastically\Client;

#[AsCommand(
    name: 'app:creat-index',
    description: 'Create or refresh index for elasticsherch.',
)]
final class CreateIndexCommand extends Command
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start creat index.');

        try {
            $indexBuilder = $this->client->getIndexBuilder();
            $newIndex = $indexBuilder->createIndex(Log::INDEX);

            $indexBuilder->markAsLive($newIndex, Log::INDEX);
            $indexBuilder->speedUpRefresh($newIndex);
            $indexBuilder->purgeOldIndices(Log::INDEX);
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
            $this->logger->critical($e->getMessage(), $e->getTrace());
        }

        $output->writeln('Finish creat index.');

        return Command::SUCCESS;
    }
}
