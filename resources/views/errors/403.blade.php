<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-rose-50 to-pink-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full">

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-rose-500 to-pink-600 p-8 text-white text-center">
                <div class="mb-4">
                    <i class="bi bi-shield-lock-fill text-6xl opacity-90"></i>
                </div>
                <div class="text-7xl font-black mb-2 opacity-20 leading-none">403</div>
                <h1 class="text-2xl font-bold -mt-2">Access Denied</h1>
                <p class="text-rose-100 text-sm mt-1">You don't have permission to do this</p>
            </div>

            <!-- Content -->
            <div class="p-8">

                <!-- Specific reason -->
                <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6">
                    <i class="bi bi-exclamation-circle-fill text-rose-500 text-xl mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-semibold text-rose-800 text-sm mb-1">Why am I seeing this?</p>
                        <p class="text-rose-700 text-sm leading-relaxed">
                            @if(!empty($exception->getMessage()))
                                {{ $exception->getMessage() }}
                            @else
                                You are not authorised to access this page or perform this action.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- What to do -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="bi bi-lightbulb-fill text-yellow-500"></i>
                        What can I do?
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1.5 ml-5 list-disc">
                        <li>Make sure you are logged into the correct account</li>
                        <li>Check that you have the necessary permissions for this action</li>
                        <li>If you believe this is a mistake, contact support</li>
                    </ul>
                </div>

                <!-- Action buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                       class="flex-1 flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2.5 px-4 rounded-xl transition-colors text-sm">
                        <i class="bi bi-arrow-left"></i>
                        Go Back
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-4 rounded-xl transition-colors text-sm">
                        <i class="bi bi-house-fill"></i>
                        Dashboard
                    </a>
                </div>

            </div>

            <!-- Footer -->
            <div class="border-t border-gray-100 px-8 py-4 text-center">
                <p class="text-xs text-gray-400">
                    {{ config('app.name') }} &middot; Need help? <a href="mailto:{{ config('mail.from.address', 'support@'.parse_url(config('app.url'), PHP_URL_HOST)) }}" class="text-rose-500 hover:underline">Contact Support</a>
                </p>
            </div>

        </div>
    </div>
</body>
</html>
