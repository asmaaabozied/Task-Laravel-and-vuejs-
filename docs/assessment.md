# Flojics Technical Assessment

## Questions for the Product Owner

- Should escalation always notify both email and Slack, or only selected channels? The implementation accepts a selected channel list.
- Is there a defined ticket priority and status workflow beyond `Open` and `Escalated`?
- Should escalation be reversible or audit only? This feature assumes one-way escalation state change.
- Do we need customer-level notification preferences or should the request specify channels per escalation?

## Assumptions

- Tickets are a new domain entity in this repository.
- Escalation is performed by a POST to `/api/tickets/{id}/escalate`.
- Selected channels are provided in the request and default to both `email` and `slack`.
- Email uses the configured mail driver, with fallback to `mail.from.address` if a customer email is missing.
- Slack sends via webhook configured in `SLACK_WEBHOOK_URL`.

## Recommendations

- Add a `ticket_escalation_events` audit table for historical tracking and user details.
- Support channel configuration per customer or ticket type.
- Add a notification mechanism for `WhatsApp`, `SMS`, `Microsoft Teams`, and push by extending the channel interface.
- Add a separate ticket status enum and validation layer when more states are introduced.
- Add event logging or observability for failed queue retries.

## Database Design

### Tables created/modified

- `tickets`
  - Fields: `id`, `subject`, `description`, `priority`, `status`, `customer_name`, `customer_email`, `escalated_at`, `created_at`, `updated_at`
  - Indexes on `status` and `priority`

- `ticket_notification_logs`
  - Fields: `id`, `ticket_id`, `channel`, `status`, `attempts`, `payload`, `last_error`, `sent_at`, `created_at`, `updated_at`
  - Foreign key `ticket_id` references `tickets.id`
  - Index on `ticket_id` and `channel`

### Relationships

- `Ticket` has many `TicketNotificationLog`
- `TicketNotificationLog` belongs to `Ticket`

## Architecture Notes

### Folder structure

- `app/Models` contains the core `Ticket` and `TicketNotificationLog` entities.
- `app/Jobs` contains the queued notification job.
- `app/Notifications` contains channel abstractions and concrete delivery implementations.
- `app/Services` contains escalation orchestration logic.
- `resources/js/components` contains the Vue escalation page.
- `database/migrations` contains the schema updates.

### Design decisions

- Use a service layer (`TicketEscalationService`) to orchestrate notification log creation and job dispatch.
- Use a queue job (`SendTicketEscalationChannelJob`) with `tries = 3` to support retry attempts.
- Persist notification results in `ticket_notification_logs` for auditing and operator visibility.
- Keep the frontend UI simple and focused on ticket details and escalation action.

### Notification architecture

- Channels implement `App\Notifications\Contracts\NotificationChannel`.
- `EmailEscalationChannel` and `SlackEscalationChannel` are concrete implementations.
- The service resolves channel handlers by name and dispatches jobs for each selected channel.
- New channels are added by implementing the interface and registering the resolver in `TicketEscalationService`.

### Retry strategy

- Each channel notification is processed in its own queued job.
- Jobs are configured with `tries = 3`.
- Each attempt increments `attempts` and the final state is recorded.
- On final failure, `failed()` updates the notification log with the error.

## Testing

### Test cases

- Successful escalation updates ticket status to `Escalated` and queues notification jobs.
- Invalid ticket ID returns `404 Not Found`.
- Notification job success marks a log entry as `success`.
- Notification failure raises an exception and can be retried up to 3 times by the queue.
- Retry exhaustion results in a final `failed` status on the notification log.

### Self-testing notes

- Verified that the escalation endpoint updates the ticket status and escalation datetime.
- Validated queue job dispatch for both supported channels.
- Confirmed email and Slack channels can be resolved and send through configured transports.
- Added automated tests for API behavior and notification job execution.
