@component('mail::message')
# 💳 Wallet Credited — {{ $credits }} Credits Added!

Hi **{{ $user->name }}**,

Great news! Your deposit request has been **approved** and your wallet has been credited.

@component('mail::panel')
| | |
|---|---|
| **Credits Added** | {{ number_format($credits) }} credits |
| **Transaction ID** | `{{ $txid ?: 'N/A' }}` |
| **New Balance** | {{ number_format($user->credit_balance) }} credits |
@endcomponent

Your credits are available immediately. Use them to send gifts, tip other members, or unlock premium features.

@component('mail::button', ['url' => $walletUrl, 'color' => 'green'])
View My Wallet →
@endcomponent

Thank you for topping up,
**The {{ $appName }} Team**

---
<small>If you did not submit this deposit request, please [contact support]({{ $appUrl }}).</small>
@endcomponent
