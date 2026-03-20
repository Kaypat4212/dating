@component('mail::message')
@if($approved)
# ✅ Withdrawal Approved — Processing Now

Hi **{{ $user->name }}**,

Your withdrawal request has been **approved** and is being processed. Funds will be sent to your nominated wallet shortly.

@component('mail::panel')
| | |
|---|---|
| **Credits Withdrawn** | {{ number_format($credits) }} credits |
| **Payout To** | `{{ $destination }}` |
@if($currency)
| **Currency** | {{ $currency }}{{ $network ? ' (' . $network . ')' : '' }} |
@endif
| **Status** | ✅ Approved – processing |
@endcomponent

Payouts are typically processed within 24–48 hours. You'll receive crypto at the address listed above once it's sent.

@component('mail::button', ['url' => $walletUrl, 'color' => 'green'])
View Wallet →
@endcomponent

@else
# ❌ Withdrawal Request Rejected

Hi **{{ $user->name }}**,

Your withdrawal request of **{{ number_format($credits) }} credits** has been **rejected** and your credits have been **refunded** to your wallet.

@component('mail::panel')
| | |
|---|---|
| **Credits Refunded** | {{ number_format($credits) }} credits |
| **Destination (unused)** | `{{ $destination }}` |
| **Status** | ❌ Rejected – credits refunded |
@if($reason)
| **Reason** | {{ $reason }} |
@endif
@endcomponent

Your credits are back in your wallet and available to use or withdraw again.

@if($reason)
**Admin note:** {{ $reason }}
@endif

@component('mail::button', ['url' => $walletUrl, 'color' => 'red'])
View My Wallet →
@endcomponent

@endif

Thank you for your patience,
**The {{ $appName }} Team**

---
<small>Questions about your withdrawal? [Contact support]({{ $appUrl }}).</small>
@endcomponent
