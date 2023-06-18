@component('mail::message')
# Invitation

Accept your invitation to join the On The Dot community.

{{ $invitation->custom_message }}

@component('mail::button', ['url' => config('app.url') . '/invite/' . $invitation])
Join Now
@endcomponent

@endcomponent
