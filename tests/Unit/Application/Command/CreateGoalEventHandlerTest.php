<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command;

use App\Application\Command\CreateGoalEventCommand;
use App\Application\Command\CreateGoalEventHandler;
use App\Application\DTO\EventResponse;
use App\Infrastructure\Notification\MockEventNotifier;
use App\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateGoalEventHandlerTest extends TestCase
{
    private InMemoryEventRepository $eventRepository;

    private MockEventNotifier $eventNotifier;

    private CreateGoalEventHandler $handler;

    protected function setUp(): void
    {
        $this->eventRepository = new InMemoryEventRepository();
        $this->eventNotifier = new MockEventNotifier();
        $this->handler = new CreateGoalEventHandler(
            $this->eventRepository,
            $this->eventNotifier,
        );
    }

    #[Test]
    public function itCreatesGoalEvent(): void
    {
        $command = new CreateGoalEventCommand(
            matchId: 'match-1',
            teamId: 'arsenal',
            scorer: 'saka',
            assistant: null,
            minute: 23,
            second: 0,
        );

        $response = ($this->handler)($command);

        self::assertInstanceOf(EventResponse::class, $response);
        self::assertSame('goal', $response->data['type']);
    }

    #[Test]
    public function itPersistsEvent(): void
    {
        $command = new CreateGoalEventCommand(
            matchId: 'match-1',
            teamId: 'arsenal',
            scorer: 'saka',
            assistant: null,
            minute: 23,
            second: 0,
        );

        ($this->handler)($command);

        $events = $this->eventRepository->findAll();
        self::assertCount(1, $events);
    }

    #[Test]
    public function itNotifiesAboutEvent(): void
    {
        $command = new CreateGoalEventCommand(
            matchId: 'match-1',
            teamId: 'arsenal',
            scorer: 'saka',
            assistant: null,
            minute: 23,
            second: 0,
        );

        ($this->handler)($command);

        $notifications = $this->eventNotifier->getNotifications();
        self::assertCount(1, $notifications);
        self::assertSame('match-1', $notifications[0]['matchId']);
    }

    #[Test]
    public function itCreatesGoalWithAssistant(): void
    {
        $command = new CreateGoalEventCommand(
            matchId: 'match-1',
            teamId: 'arsenal',
            scorer: 'saka',
            assistant: 'odegaard',
            minute: 23,
            second: 15,
        );

        $response = ($this->handler)($command);

        self::assertSame('odegaard', $response->data['event']['assistant']);
    }
}
