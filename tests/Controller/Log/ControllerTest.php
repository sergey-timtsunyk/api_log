<?php

declare(strict_types=1);

namespace App\Tests\Controller\Log;

use App\Controller\Log\Controller;
use JoliCode\Elastically\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ControllerTest extends TestCase
{
    public function testAll()
    {
        $client = $this->createStub(Client::class);
        $controller = new Controller($client);

        $response = $controller->all(1, 10);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
