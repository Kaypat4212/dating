@component('mail::message')
# You're Now a Premium Member! 🌟

Hi **{{ $user->name }}**, welcome to {{ $appName }} Premium! Your **{{ ucfirst($plan) }}** plan is now active.

@component('mail::panel')
**Your Premium benefits are unlocked:**

- 👁️ See who liked your profile — no more guessing
- 💬 Unlimited messages with all your matches
- 🚀 Weekly profile boost — appear at the top of the deck
- 🌍 Browse profiles from anywhere in the world
- 🔒 Advanced privacy controls

**Active until:** {{ $expiresAt }}
@endcomponent

Make the most of it — try boosting your profile today and watch your matches soar!

@component('mail::button', ['url' => $appUrl . '/discover', 'color' => 'red'])
Start Exploring →
@endcomponent

Enjoy every perk,
**The {{ $appName }} Team**

---
<small>Questions about your subscription? Visit your [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
