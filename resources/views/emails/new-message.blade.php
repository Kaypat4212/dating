@component('mail::message')
# New Message from {{ $sender->name }} 💬

Hi **{{ $user->name }}**, you have a new message waiting!

@component('mail::panel')
**{{ $sender->name }} says:**

*"{{ $preview }}"*
@endcomponent

Don't leave them waiting — log in and reply now!

@component('mail::button', ['url' => $conversationUrl, 'color' => 'red'])
Read & Reply
@endcomponent

Happy chatting,
**The {{ $appName }} Team**

---
<small>You're receiving this because you have email notifications enabled. Manage your preferences in [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
