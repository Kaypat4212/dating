@component('mail::message')
# Someone Liked Your Profile! 😍

Hi **{{ $user->name }}**, great news — someone just liked your profile on {{ $appName }}!

@component('mail::panel')
**Want to know who it is?**

Upgrade to Premium to see exactly who liked you, send unlimited messages, and boost your profile to the top. 🚀
@endcomponent

You could be one step away from your perfect match!

@component('mail::button', ['url' => $appUrl . '/premium', 'color' => 'red'])
See Who Liked You →
@endcomponent

Don't miss your chance,
**The {{ $appName }} Team**

---
<small>Manage your notification preferences in [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
