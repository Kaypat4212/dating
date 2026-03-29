@component('mail::message')
# Wallet Funding Approval Required

A new wallet funding request needs admin review.

@component('mail::panel')
| | |
|---|---|
| **Request ID** | #{{ $fundingRequest->id }} |
| **User** | {{ $fundingRequest->user?->name }} ({{ $fundingRequest->user?->email }}) |
| **Credits Requested** | {{ number_format((int) $fundingRequest->amount) }} credits |
| **TXID** | {{ $fundingRequest->txid ?: 'N/A' }} |
| **Submitted** | {{ $fundingRequest->created_at?->format('Y-m-d H:i:s') }} UTC |
@endcomponent

@component('mail::button', ['url' => $approveUrl, 'color' => 'green'])
Approve Funding
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'red'])
Reject Funding
@endcomponent

Thanks,
{{ $appName }}
@endcomponent
