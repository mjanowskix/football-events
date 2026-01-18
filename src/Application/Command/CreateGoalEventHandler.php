<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\EventResponse;
use App\Domain\Entity\GoalEvent;
use App\Domain\Event\DomainEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Service\EventNotifierInterface;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateGoalEventHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private EventNotifierInterface $eventNotifier,
    ) {
    }

    public function __invoke(CreateGoalEventCommand $command): EventResponse
    {
        $matchId = MatchId::fromString($command->matchId);
        $teamId = TeamId::fromString($command->teamId);
        $scorer = PlayerId::fromString($command->scorer);
        $assistant = $command->assistant !== null ? PlayerId::fromString($command->assistant) : null;
        $minute = Minute::create($command->minute, $command->second);

        $event = GoalEvent::create(
            $matchId,
            $teamId,
            $scorer,
            $assistant,
            $minute,
        );

        $this->eventRepository->save($event);
        $this->dispatchDomainEvents($event->pullDomainEvents(), $command->matchId);

        return EventResponse::fromEntity($event);
    }

    /**
     * @param DomainEvent[] $domainEvents
     */
    private function dispatchDomainEvents(array $domainEvents, string $matchId): void
    {
        foreach ($domainEvents as $domainEvent) {
            $this->eventNotifier->notifyMatch($matchId, $domainEvent);
        }
    }
}
