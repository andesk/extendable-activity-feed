# extendable-activity-feed

This project aims to provide a conceptual skeleton of an activity feed know from social networks mainly that will be extendable and therefore integrateable into more or less any PHP application by implementing custom needs depending on the use case and the used tech stack of the given project.

## Concepts

- **Activity**: Let's define an activity as an event triggered by a user that is worth being displayed in the activity feed of that user or in an aggregated feed of activities of a group of users. It can be anything from users creating a post to users liking post, users following other users, users commenting on posts, etc. In the end, what is considered an activity is up to you as the developer integrating this library into your project.
- **Activity Feed**: An activity feed is a list of activities that are relevant to a user. It might be the activities of a user profile, a group of users, like followed users, or filtered and aggregated somehow differently. Again, it will be up to you as the developer integrating this library into your project to define what is relevant to your project.

### Extendability

The library has "extendable" even in its name, so what is meant by that?
The actual logic of this library isn't massive, it's rather it's clean structure (hopefully) and well chosen (hopefully) entry points for extending the concept to custom needs. The usage of the Activity Service should be agnostic to framework specifics. Via the defined interfaces of extension entry points, you should be able to extend the library in a way that will allow you to integrate it into your php project, regardless if it's Symfony, Laravel, custom php project, etc.

If you are missing a conceptual entry point, please raise it and it might be added in the future.

The concepts for achieving this right now are:
TBD(ecided), TBD(ocumented)

## Requirements

- PHP 8.2
- Doctrine ORM for now for the library's internal use. You might not use Doctrine in your project and this *should* be fine. Internally, for now, we are using Doctrine ORM. This might change in the future.
