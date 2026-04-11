/**
 * Analytics Helper Functions
 * 
 * Usage in Blade templates:
 * {!! analytics_track('button_click', ['button_name' => 'Subscribe']) !!}
 */

if (!function_exists('analytics_track')) {
    /**
     * Track a custom analytics event
     *
     * @param string $event Event name
     * @param array $params Event parameters
     * @return string HTML script tag
     */
    function analytics_track(string $event, array $params = []): string
    {
        return \App\Services\Analytics::track($event, $params);
    }
}

if (!function_exists('analytics_page_view')) {
    /**
     * Track a page view
     *
     * @param string $path Page path
     * @param array $params Additional parameters
     * @return string HTML script tag
     */
    function analytics_page_view(string $path, array $params = []): string
    {
        return \App\Services\Analytics::trackPageView($path, $params);
    }
}

if (!function_exists('analytics_purchase')) {
    /**
     * Track a purchase/transaction
     *
     * @param float $value Transaction value
     * @param string $currency Currency code (default: USD)
     * @param string $item Item name
     * @return string HTML script tag
     */
    function analytics_purchase(float $value, string $currency = 'USD', string $item = ''): string
    {
        return \App\Services\Analytics::trackPurchase($value, $currency, $item);
    }
}

if (!function_exists('analytics_enabled')) {
    /**
     * Check if analytics is enabled
     *
     * @return bool
     */
    function analytics_enabled(): bool
    {
        $gaEnabled = filter_var(\App\Models\SiteSetting::get('google_analytics_enabled'), FILTER_VALIDATE_BOOLEAN);
        $firebaseEnabled = filter_var(\App\Models\SiteSetting::get('firebase_enabled'), FILTER_VALIDATE_BOOLEAN);
        
        return $gaEnabled || $firebaseEnabled;
    }
}
