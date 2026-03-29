@component('mail::message')
# Funding Approval Required

A new premium funding request needs admin review.

@component('mail::panel')
| | |
|---|---|
| **Payment ID** | #{{ $payment->id }} |
| **User** | {{ $payment->user?->name }} ({{ $payment->user?->email }}) |
| **Plan** | {{ $payment->plan_label }} |
| **Amount** | ${{ number_format((float) $payment->amount, 2) }} |
| **Crypto** | {{ $payment->crypto_currency ?: 'N/A' }} |
| **TX Hash** | {{ $payment->tx_hash ?: 'N/A' }} |
| **Submitted** | {{ $payment->created_at?->format('Y-m-d H:i:s') }} UTC |
@endcomponent

@component('mail::button', ['url' => $approveUrl, 'color' => 'green'])
Approve Payment
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'red'])
Reject Payment
@endcomponent

Thanks,
{{ $appName }}
@endcomponent
