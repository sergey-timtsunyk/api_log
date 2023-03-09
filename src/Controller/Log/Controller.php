<?php

declare(strict_types=1);

namespace App\Controller\Log;

use App\Log\Model\Log;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Result;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;

#[Route(path: '/log', name: 'log_')]
#[IsGranted('ROLE_USER')]
final class Controller
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    #[Get(path: '', name: 'all')]
    #[QueryParam(name: 'page', requirements: "\d+", default: '1', description: 'Page of the overview.')]
    #[QueryParam(name: 'count', requirements: "\d+", default: '10', description: 'Count of page.')]
    public function all(int $page, int $count): Response
    {
        $response = new ResponseData();
        $query = [
            'from' => $page * $count,
            'size' => $count,
        ];

        $total = $this->client->getIndex(Log::INDEX)->count();
        $results = $this->client->getIndex(Log::INDEX)->search($query);

        $response->setPage($total, $page, $count);

        /** @var Result $result */
        foreach ($results as $result) {
            $response->add($result->getModel());
        }

        return new JsonResponse($response, 200, ['Content-Type' => 'application/json']);
    }
}
