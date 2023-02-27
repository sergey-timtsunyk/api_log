<?php

declare(strict_types=1);

namespace App\Log\Model;

final class Log
{
    public const INDEX = 'logs';

    public function __construct(
        public readonly string $message,
        public readonly string $host,
        public readonly string $user,
        public readonly string $userAuth,
        public readonly string $time,
        public readonly string $method,
        public readonly string $request,
        public readonly string $protocol,
        public readonly int $status,
        public readonly int $bytes,
    ) {
    }
}
