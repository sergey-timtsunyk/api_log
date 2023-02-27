<?php

declare(strict_types=1);

namespace App\Component\Logs;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

interface ReadLogs
{
    public function open(ContainerBagInterface $params): void;

    public function read(callable $call): void;

    public function close(): void;
}
