# ADR-0002: Use CQRS Pattern with Symfony Messenger

## Status

Accepted

## Context

The application needs to:
1. Handle write operations (creating events)
2. Handle read operations (fetching statistics)
3. Support future scalability for high event volumes
4. Maintain clean separation between reads and writes

## Decision

We will implement **Light CQRS** using **Symfony Messenger** with two separate buses:

```yaml
messenger:
    buses:
        command.bus:
            middleware:
                - doctrine_transaction
        query.bus: ~
```

### Commands (Write Side)
- `CreateGoalEventCommand` → `CreateGoalEventHandler`
- `CreateFoulEventCommand` → `CreateFoulEventHandler`

### Queries (Read Side)
- `GetMatchStatisticsQuery` → `GetMatchStatisticsHandler`
- `GetTeamStatisticsQuery` → `GetTeamStatisticsHandler`

## Alternatives Considered

### 1. Simple Service Layer
- Pros: Less code, simpler
- Cons: Mixes read/write concerns, harder to scale

### 2. Full Event Sourcing
- Pros: Complete audit trail, temporal queries
- Cons: Overkill for this scope, complex implementation

### 3. Custom Command Bus
- Pros: No framework dependency
- Cons: Reinventing the wheel, less maintainable

## Consequences

### Positive
- Clear separation of read and write operations
- Commands wrapped in database transactions automatically
- Easy to add async processing later (just change transport)
- Handlers are small, focused, and testable
- Symfony ecosystem integration

### Negative
- Additional abstraction layer
- Slight performance overhead from message dispatch
- Requires understanding of Messenger component

### Implementation Notes
- Using `HandleTrait` in controllers for synchronous dispatch
- Commands return `EventResponse` DTO (not pure CQRS, but pragmatic)
- Queries return `StatisticsResponse` DTO
