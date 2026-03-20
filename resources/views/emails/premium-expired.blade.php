@component('mail::message')
# Your Premium Has Expired 💔

Hi **{{ $user->name }}**, your {{ $appName }} Premium subscription has expired and your account is now on the free plan.

@component('mail::panel')
**You're missing out on:**

- 👁️ See who liked your profile
- 💬 Unlimited messaging
- 🚀 Profile boost
- 🌍 Global browsing
@endcomponent

Your matches are still here — renew today and get straight back to connecting!

@component('mail::button', ['url' => $appUrl . '/premium', 'color' => 'red'])
Renew Premium →
@endcomponent

We hope to see you back at full speed soon,
**The {{ $appName }} Team**

---
<small>Manage your account at [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
