@component('mail::message')
# Re: {{ $type }} — {{ $record->title }}

Hi {{ $record->name }},

Thank you for taking the time to submit your {{ strtolower($type) }}. Here is our response:

@component('mail::panel')
{{ $response }}
@endcomponent

**Your submission:**

> **{{ $record->title }}**
> {{ $record->body }}

---

@component('mail::button', ['url' => route('pages.feature-request')])
Submit Another Request
@endcomponent

Thanks again for helping us improve {{ $siteName }}!

Warm regards,
**The {{ $siteName }} Team**

---
<small>This is a reply to a {{ strtolower($type) }} you submitted at {{ $siteName }}. If you did not submit this, please ignore this email.</small>
@endcomponent
