# ADR-0004: Use Domain Events for Side Effects

## Status

Accepted

## Context

When a goal or foul is recorded, several things need to happen:
1. Event is persisted
2. Statistics are updated
3. Clients are notified in real-time

Coupling all these concerns in a single handler violates SRP and makes testing difficult.

## Decision

We will use **Domain Events** to decouple side effects:

```php
// Domain Events
final readonly class GoalScored implements DomainEvent
{
    public function __construct(
        public EventId $eventId,
        public MatchId $matchId,
        public TeamId $teamId,
        public PlayerId $scorer,
        public ?PlayerId $assistant,
        public Minute $minute,
        public DateTimeImmutable $occurredAt,
    ) {}
}

final readonly class FoulCommitted implements DomainEvent { /* ... */ }
```

### Event Recording
Entities record events internally:
```php
class GoalEvent extends FootballEvent
{
    public static function create(/* ... */): self
    {
        $event = new self(/* ... */);
        $event->recordDomainEvent(new GoalScored(/* ... */));
        return $event;
    }
}
```

### Event Dispatch
Command handlers pull and dispatch events:
```php
$goalEvent = GoalEvent::create(/* ... */);
$this->eventRepository->save($goalEvent);

foreach ($goalEvent->pullDomainEvents() as $domainEvent) {
    $this->eventNotifier->notifyMatch($matchId, $domainEvent);
}
```

## Alternatives Considered

### Direct Service Calls
```php
$this->statisticsService->incrementGoals($matchId, $teamId);
$this->notificationService->notify($event);
```
- Cons: Tight coupling, handler knows about all side effects

### Symfony Event Dispatcher
- Pros: Native Symfony integration
- Cons: Mixes infrastructure events with domain events

## Consequences

### Positive
- **Decoupling**: Handlers don't know about notification mechanism
- **Testability**: Can test event recording without side effects
- **Extensibility**: Easy to add new listeners
- **Audit Trail**: Events capture what happened in domain terms

### Negative
- Additional abstraction
- Domain events are not persisted (not full Event Sourcing)

### Note on Naming
`GoalEvent` (entity) vs `GoalScored` (domain event):
- Entity = the persisted record of a goal
- Domain Event = notification that a goal was scored
- Different concepts, similar names - could be confusing
