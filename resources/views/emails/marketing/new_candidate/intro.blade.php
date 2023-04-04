@component('mail::message')

Hi {{ $candidate->first_name }},

Congratulations on becoming a candidate for {{ $candidate->fullOffice }}!

I'm writing from Community Fluency, a Massachusetts-based company that helps candidates with their elections.

@component('mail::button', ['url' => 'http://www.campaignfluency.com/?candidate_email_response='.base64_encode($candidate->id)])
Check out the Website
@endcomponent

Feel free to give me a call!

Thanks,<br>
Peri O'Connor<br />
Community Fluency<br />
(617) 000-000

[Click if you don't want us to email you]({{ url('http://www.campaignfluency.com/?candidate_email_unsubscribe='.base64_encode($candidate->id)) }})

@endcomponent


