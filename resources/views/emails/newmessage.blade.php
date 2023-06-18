@component('mail::message')
# New Message

A new nessage is waiting for you at On The Dot.

@component('mail::button', ['url' => config('app.url') . '/messages'])
Read message
@endcomponent

@endcomponent
