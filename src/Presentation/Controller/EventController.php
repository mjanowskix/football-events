<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Command\CreateFoulEventCommand;
use App\Application\Command\CreateGoalEventCommand;
use App\Application\DTO\EventResponse;
use App\Domain\Exception\InvalidEventDataException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class EventController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route('/events', name: 'api_create_event', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['type'])) {
            return $this->json(['error' => 'Event type is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $response = match ($data['type']) {
                'goal' => $this->handleGoalEvent($data),
                'foul' => $this->handleFoulEvent($data),
                default => throw InvalidEventDataException::invalidType($data['type']),
            };

            return $this->json($response->toArray(), Response::HTTP_CREATED);
        } catch (InvalidEventDataException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function handleGoalEvent(array $data): EventResponse
    {
        $this->validateRequiredFields($data, ['match_id', 'team_id', 'scorer', 'minute']);

        $command = new CreateGoalEventCommand(
            matchId: $data['match_id'],
            teamId: $data['team_id'],
            scorer: $data['scorer'],
            assistant: $data['assistant'] ?? null,
            minute: (int) $data['minute'],
            second: (int) ($data['second'] ?? 0),
        );

        /** @var EventResponse */
        return $this->handle($command);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function handleFoulEvent(array $data): EventResponse
    {
        $this->validateRequiredFields($data, ['match_id', 'team_id', 'player', 'minute']);

        $command = new CreateFoulEventCommand(
            matchId: $data['match_id'],
            teamId: $data['team_id'],
            player: $data['player'],
            affectedPlayer: $data['affected_player'] ?? null,
            minute: (int) $data['minute'],
            second: (int) ($data['second'] ?? 0),
        );

        /** @var EventResponse */
        return $this->handle($command);
    }

    /**
     * @param array<string, mixed> $data
     * @param string[] $fields
     */
    private function validateRequiredFields(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw InvalidEventDataException::missingField($field);
            }
        }
    }
}
