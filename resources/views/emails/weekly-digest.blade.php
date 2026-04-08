<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Weekly Dating Summary</title>
<style>
  body { margin:0; padding:0; background:#f4f4f8; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:#1e1b2e; }
  .wrap { max-width:580px; margin:0 auto; }
  .header { background:linear-gradient(135deg,#f43f5e 0%,#ec4899 50%,#a855f7 100%); border-radius:0 0 24px 24px; padding:40px 32px 36px; text-align:center; }
  .header-emoji { font-size:3rem; display:block; margin-bottom:12px; }
  .header h1 { margin:0 0 6px; font-size:1.6rem; font-weight:800; color:#fff; letter-spacing:-.02em; }
  .header p  { margin:0; font-size:.92rem; color:rgba(255,255,255,.85); }
  .body { padding:28px 24px; }
  .greeting { font-size:1rem; color:#374151; margin-bottom:24px; }
  .stats-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:24px; }
  .stat { background:#fff; border:1.5px solid #f1f0f5; border-radius:16px; padding:18px 12px; text-align:center; }
  .stat-icon { font-size:1.6rem; display:block; margin-bottom:6px; }
  .stat-num  { display:block; font-size:1.9rem; font-weight:800; color:#f43f5e; line-height:1; }
  .stat-label{ display:block; font-size:.7rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.07em; margin-top:4px; }
  .stat.views .stat-num { color:#a855f7; }
  .stat.matches .stat-num { color:#ec4899; }
  .banner { border-radius:14px; padding:16px 20px; margin-bottom:20px; }
  .banner.tip   { background:#fdf4ff; border:1.5px solid #e9d5ff; }
  .banner.warn  { background:#fff7ed; border:1.5px solid #fed7aa; }
  .banner-title { font-size:.85rem; font-weight:700; margin-bottom:5px; color:#7c3aed; }
  .banner.warn .banner-title { color:#c2410c; }
  .banner p { margin:0; font-size:.82rem; color:#4b5563; line-height:1.55; }
  .cta-wrap { text-align:center; margin:24px 0 20px; }
  .cta { display:inline-block; background:linear-gradient(135deg,#f43f5e,#a855f7); color:#fff; text-decoration:none; font-weight:700; font-size:.95rem; padding:14px 36px; border-radius:50px; }
  .divider { border:none; border-top:1px solid #ede9f6; margin:20px 0; }
  .tips-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:#9ca3af; margin-bottom:12px; }
  .tip-row { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; }
  .tip-dot { width:26px; height:26px; background:#fdf4ff; border:1.5px solid #e9d5ff; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.85rem; margin-top:1px; }
  .tip-text { font-size:.82rem; color:#4b5563; line-height:1.55; }
  .footer { text-align:center; padding:20px 24px 32px; font-size:.72rem; color:#9ca3af; }
  .footer a { color:#a855f7; text-decoration:none; }
  @media(max-width:480px){
    .stats-grid { grid-template-columns:1fr 1fr; }
    .stat.views { grid-column:1/-1; }
    .body { padding:20px 16px; }
    .header { padding:28px 20px; }
  }
</style>
</head>
<body>
<div class="wrap">

  {{-- ── Header ─────────────────────────────────────────────── --}}
  <div class="header">
    <span class="header-emoji">💌</span>
    <h1>Your Week in Love</h1>
    <p>{{ $stats['week_start'] }} – {{ $stats['week_end'] }}</p>
  </div>

  <div class="body">

    <p class="greeting">Hi <strong>{{ $user->name }}</strong> 👋</p>

    {{-- ── Stat cards ─────────────────────────────────────────── --}}
    <div class="stats-grid">
      <div class="stat likes">
        <span class="stat-icon">❤️</span>
        <span class="stat-num">{{ $stats['likes_this_week'] }}</span>
        <span class="stat-label">New Likes</span>
      </div>
      <div class="stat matches">
        <span class="stat-icon">💕</span>
        <span class="stat-num">{{ $stats['matches_this_week'] }}</span>
        <span class="stat-label">New Matches</span>
      </div>
      <div class="stat views">
        @if($stats['is_premium'] && $stats['views_this_week'] !== null)
          <span class="stat-icon">👀</span>
          <span class="stat-num">{{ $stats['views_this_week'] }}</span>
          <span class="stat-label">Profile Views</span>
        @else
          <span class="stat-icon">🔒</span>
          <span class="stat-num" style="font-size:1.1rem;color:#a855f7">Premium</span>
          <span class="stat-label">Unlock Views</span>
        @endif
      </div>
    </div>

    {{-- ── Contextual banner ──────────────────────────────────── --}}
    @if($stats['matches_this_week'] > 0)
    <div class="banner tip">
      <div class="banner-title">🎉 You matched this week!</div>
      <p>You have {{ $stats['matches_this_week'] }} new {{ $stats['matches_this_week'] === 1 ? 'match' : 'matches' }} waiting. Don't leave them hanging — send a message now while the spark is fresh!</p>
    </div>
    @elseif($stats['likes_this_week'] > 0)
    <div class="banner tip">
      <div class="banner-title">😍 People are noticing you!</div>
      <p>{{ $stats['likes_this_week'] }} {{ $stats['likes_this_week'] === 1 ? 'person' : 'people' }} liked your profile this week. Like them back to turn it into a match!</p>
    </div>
    @elseif(! $stats['profile_complete'])
    <div class="banner warn">
      <div class="banner-title">⚠️ Your profile needs attention</div>
      <p>Profiles with a complete bio, photos, and interests get <strong>3× more likes</strong>. Take 2 minutes to fill yours in!</p>
    </div>
    @else
    <div class="banner tip">
      <div class="banner-title">💡 Stay visible!</div>
      <p>Logging in daily and swiping keeps your profile at the top of the stack. Try sending a wave to someone who caught your eye!</p>
    </div>
    @endif

    {{-- ── CTA button ────────────────────────────────────────── --}}
    <div class="cta-wrap">
      @if($stats['matches_this_week'] > 0)
        <a class="cta" href="{{ $appUrl }}/messages">Open Messages &rarr;</a>
      @else
        <a class="cta" href="{{ $appUrl }}/discover">Browse Profiles &rarr;</a>
      @endif
    </div>

    <hr class="divider">

    {{-- ── Weekly tips ────────────────────────────────────────── --}}
    <div class="tips-title">💡 Tips to boost your week</div>
    <div class="tip-row">
      <div class="tip-dot">📸</div>
      <div class="tip-text"><strong>Add more photos.</strong> Profiles with 5+ photos get significantly more right-swipes. Your best smile goes a long way.</div>
    </div>
    <div class="tip-row">
      <div class="tip-dot">👋</div>
      <div class="tip-text"><strong>Send a wave.</strong> It's a no-pressure way to show interest — they'll get a notification and may just like you back.</div>
    </div>
    @if(! $stats['is_premium'])
    <div class="tip-row">
      <div class="tip-dot">⭐</div>
      <div class="tip-text"><strong>Go Premium</strong> to see who viewed your profile, send unlimited messages, and get a Spotlight boost. <a href="{{ $appUrl }}/premium" style="color:#a855f7;font-weight:600">Learn more &rarr;</a></div>
    </div>
    @endif
    <div class="tip-row">
      <div class="tip-dot">✨</div>
      <div class="tip-text"><strong>Try the Vibe Check Quiz.</strong> Matched users who share quiz answers are 2× more likely to spark a conversation.</div>
    </div>

  </div>

  {{-- ── Footer ─────────────────────────────────────────────── --}}
  <div class="footer">
    <p style="margin:0 0 8px">You're receiving this because you're signed up on <strong>{{ $appName }}</strong>.</p>
    <p style="margin:0">
      <a href="{{ $appUrl }}/account/settings">Manage email preferences</a>
      &nbsp;·&nbsp;
      <a href="{{ $appUrl }}">Visit {{ $appName }}</a>
    </p>
  </div>

</div>
</body>
</html>
