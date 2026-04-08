@component('mail::message')
# {{ $sender->name }} sent you a message 💬

Hi **{{ $user->name }}**, you have a new message waiting for you on **{{ $appName }}**!

@component('mail::panel')
**From:** {{ $sender->name }}

*The message content is private — tap the button below to read it.*
@endcomponent

Don't leave them waiting — log in and reply now!

@component('mail::button', ['url' => $conversationUrl, 'color' => 'red'])
View Message
@endcomponent

Happy chatting,
**The {{ $appName }} Team**

---
<small>You're receiving this because you have email notifications enabled. Manage your preferences in [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
