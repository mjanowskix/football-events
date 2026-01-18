# Football Events Application

Application for handling football events (goals, fouls) with real-time statistics.

## Architecture

**Symfony 7.2 + DDD + Light CQRS**

```
src/
├── Application/           # Commands, Queries, Handlers, DTOs
├── Domain/               # Entities, Value Objects, Domain Events, Interfaces
├── Infrastructure/       # Doctrine repositories, Notifications
└── Presentation/         # Controllers
```

### Key Decisions
- **Symfony Messenger** for CQRS (command.bus, query.bus)
- **Doctrine ORM** with XML mapping (clean domain, no ORM attributes)
- **SQLite** for simplicity (file-based, no external DB)
- **Custom Doctrine Types** for Value Objects (EventId, MatchId, etc.)
- **Single Table Inheritance** for GoalEvent/FoulEvent

📄 See [Architecture Decision Records](docs/adr/README.md) for detailed rationale.

## Requirements

- Docker
- Docker Compose

## Installation

```bash
# Build and start
docker compose up --build -d

# Create database schema
docker exec football_events_app php bin/console doctrine:schema:create
```

Application: `http://localhost:8000`

## API Endpoints

### Create Goal Event

```bash
curl -X POST http://localhost:8000/api/events \
  -H "Content-Type: application/json" \
  -d '{
    "type": "goal",
    "match_id": "m1",
    "team_id": "arsenal",
    "scorer": "Saka",
    "assistant": "Odegaard",
    "minute": 23,
    "second": 15
  }'
```

### Create Foul Event

```bash
curl -X POST http://localhost:8000/api/events \
  -H "Content-Type: application/json" \
  -d '{
    "type": "foul",
    "match_id": "m1",
    "team_id": "chelsea",
    "player": "Silva",
    "affected_player": "Saka",
    "minute": 45,
    "second": 30
  }'
```

### Get Match Statistics

```bash
# All teams in match
curl "http://localhost:8000/api/statistics?match_id=m1"

# Specific team
curl "http://localhost:8000/api/statistics?match_id=m1&team_id=arsenal"
```

### Example Response

```json
{
  "match_id": "m1",
  "statistics": {
    "arsenal": { "goals": 2, "fouls": 1 },
    "chelsea": { "goals": 1, "fouls": 3 }
  }
}
```

## Tests

```bash
# Run all tests
docker exec football_events_app vendor/bin/phpunit

# Run with coverage
docker exec football_events_app vendor/bin/phpunit --testdox
```

**Test Coverage:**
- 46 tests, 106 assertions
- Unit tests: Value Objects, Entities, Handlers
- Functional tests: API endpoints

## Code Quality

```bash
# PHP-CS-Fixer (PSR-12 + Symfony)
docker exec football_events_app vendor/bin/php-cs-fixer fix --dry-run

# PHPStan Level 8
docker exec football_events_app vendor/bin/phpstan analyse
```

## Project Structure

```
src/
├── Application/
│   ├── Command/          # CreateGoalEventCommand, CreateFoulEventCommand + Handlers
│   ├── Query/            # GetMatchStatisticsQuery, GetTeamStatisticsQuery + Handlers
│   └── DTO/              # EventResponse, StatisticsResponse
├── Domain/
│   ├── Entity/           # GoalEvent, FoulEvent (Aggregate Roots)
│   ├── ValueObject/      # EventId, MatchId, TeamId, PlayerId, Minute
│   ├── Event/            # GoalScored, FoulCommitted (Domain Events)
│   ├── Repository/       # EventRepositoryInterface, StatisticsRepositoryInterface
│   ├── Service/          # EventNotifierInterface
│   └── Exception/        # InvalidEventDataException
├── Infrastructure/
│   ├── Persistence/
│   │   ├── Doctrine/     # DoctrineEventRepository, DoctrineStatisticsRepository, Types
│   │   └── InMemory/     # InMemoryEventRepository, InMemoryStatisticsRepository
│   └── Notification/     # MockEventNotifier
└── Presentation/
    └── Controller/       # EventController, StatisticsController

config/doctrine/entity/   # XML mapping files (FootballEvent, GoalEvent, FoulEvent)
```

## Real-time Notifications

The `EventNotifierInterface` provides abstraction for real-time event delivery.
Current implementation (`MockEventNotifier`) logs events. Production would use:
- Mercure
- WebSocket
- Server-Sent Events

## Further Development

- [ ] Real-time notifications (Mercure/WebSocket)
- [ ] More event types (yellow/red cards, substitutions)
- [ ] Match lifecycle management
- [ ] API authentication
- [ ] OpenAPI documentation

## AI Tools Usage

This solution was developed with assistance from Claude (Anthropic) as a productivity tool.

**My decisions and contributions:**
- Architecture design (DDD + Light CQRS approach)
- Technology choices (Symfony Messenger, Doctrine XML mapping, Value Objects)
- Code structure and organization
- Business logic implementation
- Code style and conventions (PSR-12, Symfony standards)
- Test scenarios and coverage strategy

**AI assisted with:**
- Boilerplate code generation (test files, repetitive structures)
- Symfony/Doctrine configuration troubleshooting
- Code review suggestions and spotting inconsistencies

All code was reviewed, understood, and adjusted to match project conventions.
