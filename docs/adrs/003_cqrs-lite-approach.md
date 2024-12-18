 # CQRS-lite Approach with Facade Pattern

* Status: accepted
* Deciders: Andreas Kleemann
* Date: 2024-12-18

Technical Story: Implementing a clean separation between read and write operations in the activity feed system while maintaining simplicity.

## Context and Problem Statement

How can we structure the activity feed codebase to maintain a clear separation between read and write operations without introducing the full complexity of CQRS (Command Query Responsibility Segregation)?

## Decision Drivers

* Need for clear separation of read and write operations
* Desire to keep the codebase maintainable and understandable
* Potential future extensibility requirements
* Avoiding over-engineering and unnecessary complexity
* Need for a clean and intuitive API for consumers

## Considered Options

* Full CQRS with Command/Event Handlers and separate read/write models
* Traditional layered architecture with mixed read/write services
* CQRS-lite with Command/Query separation but unified model
* Simple CRUD approach

## Decision Outcome

Chosen option: "CQRS-lite with Command/Query separation but unified model", because it provides a good balance between separation of concerns and simplicity, while keeping the door open for future evolution.

### Positive Consequences

* Clear separation between read (Query) and write (Command) operations in the codebase structure
* Simplified reasoning about data flow and operation responsibilities
* Easy to extend either side (Command or Query) independently
* Maintainable codebase with clear boundaries
* Unified model reduces complexity compared to full CQRS

### Negative Consequences

After weighing options, we accept the following consequences:

* No event sourcing capabilities out of the box
* Potential duplication of some domain logic between Command and Query sides
* Less flexibility compared to full CQRS for complex scaling scenarios
* Some CQRS benefits like separate optimization of read/write models are not fully realized

## Pros and Cons of the Options

### Full CQRS with Command/Event Handlers

* Good, because provides maximum flexibility for future scaling
* Good, because enables event sourcing if needed
* Good, because allows separate optimization of read and write models
* Bad, because introduces significant complexity
* Bad, because requires more infrastructure
* Bad, because overkill for current requirements

### Traditional Layered Architecture

* Good, because simple and well-understood
* Good, because less initial development effort
* Bad, because mixes read and write concerns
* Bad, because harder to optimize for specific read or write scenarios
* Bad, because can lead to bloated services

### CQRS-lite with Command/Query separation (chosen)

* Good, because provides clear separation of concerns
* Good, because maintains simplicity through unified model
* Good, because uses familiar Facade pattern for clean API
* Good, because allows independent evolution of read and write sides
* Bad, because some CQRS benefits are not fully realized
* Bad, because potential for some logic duplication

### Simple CRUD Approach

* Good, because very simple to implement
* Good, because lowest initial development effort
* Bad, because no separation of concerns
* Bad, because harder to maintain as complexity grows
* Bad, because difficult to optimize for specific use cases

## Implementation Details

The implementation follows these key principles:

1. **Directory Structure**:
   ```
   src/Domain/
   ├── Command/              # Write operations
   │   ├── ActivityPersister.php
   │   └── Hooks/
   └── Query/               # Read operations
       ├── ActivityFetcher.php
       ├── QueryFilters/
       ├── PostProcessors/
       └── RelationResolver/
   ```

2. **Facade Pattern**:
   - `ActivityFeedFacade` provides a clean, unified API
   - Internally delegates to appropriate Command or Query components
   - Hides the complexity of the separation from consumers

3. **Command Side**:
   - Focused on write operations (persist, delete)
   - Uses hooks for extensibility
   - Clear single responsibility per class

4. **Query Side**:
   - Rich query capabilities through filters
   - Post-processing pipeline for complex data transformations
   - Relation resolution for connected entities

## Links

* [Blog Post] [Martin Fowler on CQRS](https://martinfowler.com/bliki/CQRS.html)
* [Pattern] [Facade Pattern](https://refactoring.guru/design-patterns/facade) 