# ADR-0003: Use Value Objects for Domain Identifiers

## Status

Accepted

## Context

The domain deals with various identifiers:
- Event ID
- Match ID
- Team ID
- Player ID
- Time (minute + second)

Using primitive types (`string`, `int`) leads to:
- Primitive obsession anti-pattern
- No type safety (can pass match_id where team_id expected)
- Validation scattered across codebase

## Decision

We will use **Value Objects** for all domain identifiers:

```php
final readonly class MatchId
{
    private function __construct(private string $value) {}
    
    public static function fromString(string $value): self
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidArgumentException('MatchId cannot be empty');
        }
        return new self($value);
    }
    
    public function value(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
}
```

### Value Objects Created
| Value Object | Purpose | Validation |
|--------------|---------|------------|
| `EventId` | Unique event identifier | UUID format |
| `MatchId` | Match identifier | Non-empty, max 100 chars |
| `TeamId` | Team identifier | Non-empty, max 100 chars |
| `PlayerId` | Player identifier | Non-empty, max 100 chars |
| `Minute` | Match time | 0-120 min, 0-59 sec |

## Consequences

### Positive
- **Type Safety**: Cannot accidentally swap `MatchId` and `TeamId`
- **Self-Validating**: Invalid state is impossible
- **Encapsulation**: Validation logic in one place
- **Immutability**: `readonly` classes prevent mutation
- **Domain Language**: Code reads like business language

### Negative
- More classes to maintain
- Custom Doctrine Types required for persistence
- Slight overhead vs primitives

### Implementation Notes
- All VOs use private constructor + named static factory
- Implements `equals()` for comparison
- Custom Doctrine Types map VOs to database columns
- `Minute` stores both minute and second as JSON in DB

### Example Usage
```php
// Type-safe method signature
public function create(
    MatchId $matchId,    // Cannot pass TeamId here
    TeamId $teamId,      // Cannot pass MatchId here  
    PlayerId $scorer,
    Minute $minute
): GoalEvent;
```
