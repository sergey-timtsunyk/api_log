<?php

declare(strict_types=1);

namespace App\Controller\Log;

use App\Log\Model\Log;

final class ResponseData
{
    public array $data;
    public array $page;

    public function setPage(int $total, int $page, int $count): void
    {
        $this->page = [
            'total' => $total,
            'page' => $page,
            'count' => $count,
        ];
    }

    public function add(Log $log): void
    {
        $this->data[] = $log;
    }
}
