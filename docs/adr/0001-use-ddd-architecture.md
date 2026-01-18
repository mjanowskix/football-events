# ADR-0001: Use Domain-Driven Design Architecture

## Status

Accepted

## Context

The original PoC was a simple procedural implementation with `EventHandler`, `FileStorage`, and `StatisticsManager` classes mixed together without clear separation of concerns.

For a system handling real-time football events, we need an architecture that:
- Separates business logic from infrastructure concerns
- Allows easy swapping of persistence and notification mechanisms
- Is testable and maintainable as requirements evolve
- Supports future scalability (high event volumes, multiple event types)

## Decision

We will use **Domain-Driven Design (DDD)** with a layered architecture:

```
src/
├── Application/    # Use cases, commands, queries, DTOs
├── Domain/         # Business logic, entities, value objects
├── Infrastructure/ # External concerns (DB, notifications)
└── Presentation/   # HTTP layer (controllers)
```

### Layer Responsibilities

- **Domain**: Pure business logic, no framework dependencies
- **Application**: Orchestrates use cases, depends only on domain interfaces
- **Infrastructure**: Implements domain interfaces with concrete technologies
- **Presentation**: HTTP request/response handling

## Consequences

### Positive
- Clear separation of concerns
- Domain layer is framework-agnostic and highly testable
- Easy to swap infrastructure implementations (e.g., switch DB or notification system)
- Business logic protected from framework changes

### Negative
- More boilerplate code than simple MVC
- Steeper learning curve for developers unfamiliar with DDD

### Risks
- Potential over-engineering for current scope
- Mitigated by keeping implementation "light" - no full tactical DDD patterns (Aggregates, Bounded Contexts)
