@component('mail::message')
# Welcome to {{ $appName }}, {{ $user->name }}! 💕

We're so excited to have you here. Your account is ready — your journey to finding meaningful connections starts right now.

@component('mail::panel')
**Here's how to get started:**

1. 📸 **Add your best photos** — profiles with photos get 10× more matches
2. ✏️ **Write your bio** — show your personality and what makes you unique
3. 💛 **Set your preferences** — tell us who you're looking for
4. 🔍 **Start swiping** — your perfect match could be just a swipe away
@endcomponent

The more complete your profile, the better your matches will be — so take a few minutes now to make a great impression!

@component('mail::button', ['url' => $appUrl . '/setup/step/1', 'color' => 'red'])
Complete My Profile →
@endcomponent

We can't wait to see you find your person. 💕

Warmly,
**The {{ $appName }} Team**

---
<small>You created this account using {{ $user->email }}. Visit your [account settings]({{ $appUrl }}/account/settings) to manage email preferences.</small>
@endcomponent
