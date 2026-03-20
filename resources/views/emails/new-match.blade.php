@component('mail::message')
# It's a Match! 💕

Hi **{{ $user->name }}**, you and **{{ $otherUser->name }}** liked each other — it's a match!

@component('mail::panel')
This is your moment. The first message sets the tone — be genuine, be curious, be yourself. 😊
@endcomponent

@component('mail::button', ['url' => $conversationUrl, 'color' => 'red'])
Send a Message 💬
@endcomponent

Don't keep them waiting — your perfect match is ready to chat!

With love,
**The {{ $appName }} Team**

---
<small>You matched via {{ $appName }}. Manage your notification preferences in [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
