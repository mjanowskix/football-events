<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command;

use App\Application\Command\CreateFoulEventCommand;
use App\Application\Command\CreateFoulEventHandler;
use App\Application\DTO\EventResponse;
use App\Infrastructure\Notification\MockEventNotifier;
use App\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateFoulEventHandlerTest extends TestCase
{
    private InMemoryEventRepository $eventRepository;

    private MockEventNotifier $eventNotifier;

    private CreateFoulEventHandler $handler;

    protected function setUp(): void
    {
        $this->eventRepository = new InMemoryEventRepository();
        $this->eventNotifier = new MockEventNotifier();
        $this->handler = new CreateFoulEventHandler(
            $this->eventRepository,
            $this->eventNotifier,
        );
    }

    #[Test]
    public function itCreatesFoulEvent(): void
    {
        $command = new CreateFoulEventCommand(
            matchId: 'match-1',
            teamId: 'chelsea',
            player: 'silva',
            affectedPlayer: null,
            minute: 45,
            second: 30,
        );

        $response = ($this->handler)($command);

        self::assertInstanceOf(EventResponse::class, $response);
        self::assertSame('foul', $response->data['type']);
    }

    #[Test]
    public function itPersistsEvent(): void
    {
        $command = new CreateFoulEventCommand(
            matchId: 'match-1',
            teamId: 'chelsea',
            player: 'silva',
            affectedPlayer: null,
            minute: 45,
            second: 0,
        );

        ($this->handler)($command);

        $events = $this->eventRepository->findAll();
        self::assertCount(1, $events);
    }

    #[Test]
    public function itNotifiesAboutEvent(): void
    {
        $command = new CreateFoulEventCommand(
            matchId: 'match-1',
            teamId: 'chelsea',
            player: 'silva',
            affectedPlayer: null,
            minute: 45,
            second: 0,
        );

        ($this->handler)($command);

        $notifications = $this->eventNotifier->getNotifications();
        self::assertCount(1, $notifications);
        self::assertSame('match-1', $notifications[0]['matchId']);
    }

    #[Test]
    public function itCreatesFoulWithAffectedPlayer(): void
    {
        $command = new CreateFoulEventCommand(
            matchId: 'match-1',
            teamId: 'chelsea',
            player: 'silva',
            affectedPlayer: 'saka',
            minute: 45,
            second: 30,
        );

        $response = ($this->handler)($command);

        self::assertSame('saka', $response->data['event']['affected_player']);
    }
}
