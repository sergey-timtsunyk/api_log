<?php

declare(strict_types=1);

namespace App\Log\Handler\LogIndex;

use App\Log\Model\Log;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Model\Document;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;

#[AsMessageHandler]
final class BatchHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;

    private const COUNT_BATCH_CONFIG_KEY = 'app.document_index.count_batch';
    private int $countButch;

    public function __construct(
        private readonly Client $client,
        private readonly ContainerBagInterface $params,
    ) {
        $this->countButch = (int) $this->params->get(self::COUNT_BATCH_CONFIG_KEY);
    }

    public function __invoke(Message $message)
    {
        $ack = new Acknowledger(get_debug_type($this), function () {
        });

        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        $indexer = $this->client->getIndexer();
        /**
         * @var Message      $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {
            try {
                $log = $message->getLog();
                $indexer->scheduleIndex(Log::INDEX, new Document($this->getHasForLog($log->message), $log));

                $ack->ack($message);
            } catch (\Throwable $e) {
                $ack->nack($e);
            }
        }

        $indexer->flush();
    }

    private function getHasForLog(string $message): string
    {
        return hash('ripemd160', $message);
    }

    private function shouldFlush(): bool
    {
        return $this->countButch <= \count($this->jobs);
    }
}
