<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - VPN Detected</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-rose-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-8 text-white text-center">
                <div class="mb-4">
                    <i class="bi bi-shield-x text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Access Denied</h1>
                <p class="text-red-100">VPN or Proxy Detected</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="mb-6">
                    <div class="flex items-start gap-3 mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-2xl text-red-500 mt-1"></i>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Why was I blocked?</h2>
                            <p class="text-gray-600 leading-relaxed">
                                Our security system detected that you're accessing <?php echo e(config('app.name')); ?> through a VPN, proxy, or anonymous network. 
                                For the safety and security of our community, we require all users to access our platform from their genuine location.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detection Details -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-info-circle-fill text-red-600"></i>
                        <span class="font-semibold text-red-800">Detection Details:</span>
                    </div>
                    <ul class="text-sm text-red-700 space-y-1 ml-6">
                        <li>Confidence Level: <strong><?php echo e($confidence); ?>%</strong></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($provider): ?>
                            <li>Detected Provider: <strong><?php echo e($provider); ?></strong></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <li>Your IP: <strong><?php echo e(request()->ip()); ?></strong></li>
                    </ul>
                </div>

                <!-- What to do -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="bi bi-lightbulb-fill text-yellow-500"></i>
                        What should I do?
                    </h3>
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            <span><strong>Disable your VPN or Proxy</strong> - Turn off any VPN applications, browser extensions, or proxy services you're using.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            <span><strong>Clear your browser cache</strong> - Clear cookies and cached data from your browser settings.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                            <span><strong>Try again</strong> - Once disconnected from VPN, refresh this page or try accessing <?php echo e(config('app.name')); ?> again.</span>
                        </li>
                    </ol>
                </div>

                <!-- Why we do this -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                        <i class="bi bi-shield-check"></i>
                        Why we require this
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Prevents fake accounts and scammers</li>
                        <li>• Ensures genuine location-based matching</li>
                        <li>• Protects our community from fraud</li>
                        <li>• Maintains trust and safety for all members</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?php echo e(route('home')); ?>" 
                       class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 text-center">
                        <i class="bi bi-house-door-fill mr-2"></i>
                        Return to Homepage
                    </a>
                    <a href="mailto:<?php echo e($support_email); ?>" 
                       class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 text-center">
                        <i class="bi bi-envelope-fill mr-2"></i>
                        Contact Support
                    </a>
                </div>

                <!-- Still having issues -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Having trouble? If you believe this is an error, please 
                        <a href="mailto:<?php echo e($support_email); ?>" class="text-rose-600 hover:text-rose-700 font-semibold underline">contact our support team</a> 
                        with your IP address: <code class="bg-gray-100 px-2 py-1 rounded text-xs"><?php echo e(request()->ip()); ?></code>
                    </p>
                </div>
            </div>

            <!-- Footer -->  
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    This security measure helps us create a safer dating community for everyone.
                </p>
            </div>
        </div>

        <!-- Additional info -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>
                <i class="bi bi-clock"></i>
                Detection Time: <?php echo e(now()->format('F j, Y g:i A')); ?>

            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\errors\vpn-blocked.blade.php ENDPATH**/ ?>