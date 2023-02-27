<?php

declare(strict_types=1);

namespace App\Log\Handler\LogRow;

use App\Log\Handler\LogIndex\Message as LogIndexMessage;
use App\Log\Model\Log;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class Handler
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(Message $logRow)
    {
        if (!$log = $this->createLogModel($logRow->getValue())) {
            $this->logger->warning(sprintf('Not supported match for regexp: [%s].', $logRow->getValue()));
        }
        $this->bus->dispatch(new LogIndexMessage($log));
    }

    private function createLogModel(string $logRow): ?Log
    {
        $pattern = '/^([^ ]+) ([^ ]+) ([^ ]+) (\[[^\]]+\]) "(.*) (.*) (.*)" ([0-9\-]+) ([0-9\-]+)$/';

        if (preg_match($pattern, $logRow, $matches)) {
            return new Log(
                $matches[0],
                $matches[1],
                $matches[2],
                $matches[3],
                str_replace(['[', ']'], '', $matches[4]),
                $matches[5],
                $matches[6],
                $matches[7],
                (int)$matches[8],
                (int)$matches[9],
            );
        }

        return null;
    }
}
