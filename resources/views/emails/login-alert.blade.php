@component('mail::message')
# New Login Detected

Hi **{{ $user->name }}**, we noticed a new sign-in to your {{ $appName }} account.

@component('mail::panel')
**Time:** {{ $loginTime }}
**IP Address:** {{ $ip }}
**Device / Browser:** {{ $device }}
@endcomponent

If this was you, no action is needed — enjoy the app! 💕

If you don't recognise this login, please **secure your account immediately** by changing your password.

@component('mail::button', ['url' => $appUrl . '/profile/settings', 'color' => 'red'])
Secure My Account
@endcomponent

Stay safe,
**The {{ $appName }} Team**

---
<small>You're receiving this email because login alerts are enabled for your account. You can turn them off from your account notification settings.</small>
@endcomponent
