@component('mail::message')

Someone asked for a demo.

<b>Name:</b> {{ $message->name }}
<br /><br />

<b>Email:</b> {{ $message->email }}
<br /><br />

<b>Message:</b> {{ $message->notes }}
<br /><br />

Thanks,<br />
{{ config('app.name') }}
@endcomponent
