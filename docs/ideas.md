# Ideas

## Use cases, features

## Software design

### Fetching details

- resolving relations via RelationResolverInterface
    - lazy loading of activities' referenced objects within the collection, using proxies as resolved activity objects to reduce memory footprint (future, not relevant for the PoC)

### Persisting details

- [ ] Introduce a strategy pattern for the persisting of activities.
