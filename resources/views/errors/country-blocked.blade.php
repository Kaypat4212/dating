<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white text-center">
                <div class="mb-4">
                    <i class="bi bi-globe2 text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Region Not Available</h1>
                <p class="text-blue-100">Access from your country is currently restricted</p>
            </div>

            <!-- Content -->
            <div class="p-8">

                <div class="flex items-start gap-3 mb-6">
                    <i class="bi bi-exclamation-triangle-fill text-2xl text-amber-500 mt-1"></i>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Why am I seeing this?</h2>
                        <p class="text-gray-600 leading-relaxed">
                            {{ config('app.name') }} is
                            @if($mode === 'blocklist')
                                not currently available in your region.
                            @else
                                only available in select regions at this time.
                            @endif
                            We apologise for any inconvenience.
                        </p>
                    </div>
                </div>

                <!-- Detection Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-info-circle-fill text-blue-600"></i>
                        <span class="font-semibold text-blue-800">Access Details</span>
                    </div>
                    <ul class="text-sm text-blue-700 space-y-1 ml-6">
                        <li>Detected Country: <strong>{{ $countryCode }}</strong></li>
                        <li>Your IP: <strong>{{ request()->ip() }}</strong></li>
                    </ul>
                </div>

                <!-- What to do -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="bi bi-lightbulb-fill text-yellow-500"></i>
                        Think this is a mistake?
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        If you believe your country should have access, please contact our support team with your
                        IP address and country information. Country detection is based on your IP address — if
                        you are using a corporate or shared network, your detected country may not be accurate.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-3">
                    <a href="mailto:{{ config('mail.from.address', 'support@'.parse_url(config('app.url'), PHP_URL_HOST)) }}"
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors text-sm">
                        <i class="bi bi-envelope-fill"></i>
                        Contact Support
                    </a>
                    <a href="{{ url('/') }}"
                       class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-2.5 rounded-lg transition-colors text-sm">
                        <i class="bi bi-arrow-left"></i>
                        Back to Homepage
                    </a>
                </div>

            </div><!-- /content -->

            <!-- Footer -->
            <div class="bg-gray-50 border-t px-8 py-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

        </div><!-- /card -->

    </div>
</body>
</html>
