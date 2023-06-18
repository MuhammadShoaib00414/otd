@component('mail::message')
# Your requested export has completed

{{ $export->segment->name }}

@component('mail::button', ['url' => config('app.url') . '/admin/exports/'.$export->id.'/download'])
Download
@endcomponent
@endcomponent
