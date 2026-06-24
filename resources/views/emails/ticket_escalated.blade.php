@component('mail::message')
# Ticket Escalated

Ticket **#{{ $ticket->id }}** has been escalated.

- Subject: {{ $ticket->subject }}
- Priority: {{ $ticket->priority }}
- Status: {{ $ticket->status }}
- Escalation Date: {{ $ticket->escalated_at?->toDayDateTimeString() ?? 'N/A' }}

Thanks,
{{ config('app.name') }}
@endcomponent
