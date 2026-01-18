<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\StatisticsResponse;
use App\Application\Query\GetMatchStatisticsQuery;
use App\Application\Query\GetTeamStatisticsQuery;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class StatisticsController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    #[Route('/statistics', name: 'api_get_statistics', methods: ['GET'])]
    public function getStatistics(Request $request): JsonResponse
    {
        $matchId = $request->query->getString('match_id');
        $teamId = $request->query->getString('team_id');

        if ($matchId === '') {
            return $this->json(['error' => 'match_id is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $query = $teamId !== ''
                ? new GetTeamStatisticsQuery($matchId, $teamId)
                : new GetMatchStatisticsQuery($matchId);

            /** @var StatisticsResponse */
            $response = $this->handle($query);

            return $this->json($response->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
