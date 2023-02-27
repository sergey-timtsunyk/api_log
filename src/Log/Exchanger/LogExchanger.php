<?php

declare(strict_types=1);

namespace App\Log\Exchanger;

use Elastica\Document as ElasticaDocument;
use JoliCode\Elastically\Messenger\DocumentExchangerInterface;

final class LogExchanger implements DocumentExchangerInterface
{
    public function fetchDocument(string $className, string $id): ?ElasticaDocument
    {
        return null;
    }
}
