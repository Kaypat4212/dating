<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? 'Email Preview' }}</title>
<style>
  body { margin:0; padding:0; background:#f4f4f5; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:#1a1a2e; }
  .wrapper { max-width:600px; margin:0 auto; padding:40px 16px; }
  .card { background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
  .header { background:linear-gradient(135deg,#e91e8c,#c2185b); padding:28px 32px; text-align:center; }
  .header-title { color:#fff; font-size:1.2rem; font-weight:700; margin:0; letter-spacing:.3px; }
  .body { padding:32px; line-height:1.6; font-size:15px; }
  .footer { background:#f8f9fa; border-top:1px solid #eee; padding:20px 32px; text-align:center; font-size:12px; color:#999; }
  h2 { color:#1a1a2e; margin-top:0; }
  a { color:#e91e8c; }
  code { background:#f3f4f6; border:1px solid #e5e7eb; border-radius:4px; padding:2px 6px; font-size:.88em; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <p class="header-title">{{ config('app.name') }}</p>
    </div>
    <div class="body">
      {!! $html !!}
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }} &bull; All rights reserved
    </div>
  </div>
</div>
</body>
</html>
