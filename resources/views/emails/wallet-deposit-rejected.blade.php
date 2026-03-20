@component('mail::message')
# ❌ Deposit Request Rejected

Hi **{{ $user->name }}**,

Unfortunately, your deposit request of **{{ number_format($credits) }} credits** could not be approved.

@component('mail::panel')
| | |
|---|---|
| **Credits Requested** | {{ number_format($credits) }} credits |
| **Transaction ID** | `{{ $txid ?: 'N/A' }}` |
| **Status** | ❌ Rejected |
@if($reason)
| **Reason** | {{ $reason }} |
@endif
@endcomponent

@if($reason)
**Admin note:** {{ $reason }}
@else
Your request could not be verified. This may be due to an unconfirmed transaction, incorrect TXID, or an unreadable proof image.
@endif

**What you can do:**
- Double-check your TXID and resubmit with a clear screenshot of the confirmed transaction
- Ensure the transaction has enough confirmations on the blockchain
- [Contact support]({{ $appUrl }}) if you believe this was an error

@component('mail::button', ['url' => $walletUrl, 'color' => 'red'])
Submit a New Request →
@endcomponent

We're happy to help,
**The {{ $appName }} Team**
@endcomponent
