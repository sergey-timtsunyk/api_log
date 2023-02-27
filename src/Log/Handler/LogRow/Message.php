<?php

declare(strict_types=1);

namespace App\Log\Handler\LogRow;

final class Message
{
    public function __construct(private readonly string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
