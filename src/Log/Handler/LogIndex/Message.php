<?php

declare(strict_types=1);

namespace App\Log\Handler\LogIndex;

use App\Log\Model\Log;

final class Message
{
    public function __construct(private readonly Log $log)
    {
    }

    public function getLog(): Log
    {
        return $this->log;
    }
}
