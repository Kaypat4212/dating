@props(['user'])
@php
    $isTraveling = $user->relationLoaded('travelPlans')
        ? $user->travelPlans->where('is_active', true)->where('travel_to', '>=', today()->toDateString())->isNotEmpty()
        : $user->travelPlans()->where('is_active', true)->where('travel_to', '>=', today())->exists();
@endphp
@if($isTraveling)
<span class="badge rounded-pill"
      style="background:linear-gradient(135deg,#0ea5e9,#6366f1);color:#fff;font-size:.65rem;font-weight:600;padding:3px 8px;display:inline-flex;align-items:center;gap:3px;"
      title="Currently has travel plans">
    ✈️ Traveling Soon
</span>
@endif
