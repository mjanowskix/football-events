<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\EventResponse;
use App\Domain\Entity\FoulEvent;
use App\Domain\Event\DomainEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Service\EventNotifierInterface;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateFoulEventHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private EventNotifierInterface $eventNotifier,
    ) {
    }

    public function __invoke(CreateFoulEventCommand $command): EventResponse
    {
        $matchId = MatchId::fromString($command->matchId);
        $teamId = TeamId::fromString($command->teamId);
        $playerAtFault = PlayerId::fromString($command->player);
        $affectedPlayer = $command->affectedPlayer !== null ? PlayerId::fromString($command->affectedPlayer) : null;
        $minute = Minute::create($command->minute, $command->second);

        $event = FoulEvent::create(
            $matchId,
            $teamId,
            $playerAtFault,
            $affectedPlayer,
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
