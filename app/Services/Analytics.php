<?php

namespace App\Services;

use App\Models\SiteSetting;

/**
 * Analytics Service for Google Analytics and Firebase Analytics
 * 
 * Usage:
 * Analytics::track('event_name', ['param1' => 'value1']);
 * Analytics::trackPageView('/profile/john-doe');
 * Analytics::trackPurchase(99.99, 'USD', 'Premium Subscription');
 */
class Analytics
{
    /**
     * Track a custom event
     */
    public static function track(string $event, array $params = []): string
    {
        if (!self::isEnabled()) {
            return '';
        }

        $paramsJson = json_encode($params, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        
        return self::getTrackingScript($event, $paramsJson);
    }

    /**
     * Track page view
     */
    public static function trackPageView(string $path, array $params = []): string
    {
        $params = array_merge([
            'page_path' => $path,
            'page_title' => $params['page_title'] ?? $path,
        ], $params);

        return self::track('page_view', $params);
    }

    /**
     * Track user signup
     */
    public static function trackSignup(string $method = 'email'): string
    {
        return self::track('sign_up', ['method' => $method]);
    }

    /**
     * Track user login
     */
    public static function trackLogin(string $method = 'email'): string
    {
        return self::track('login', ['method' => $method]);
    }

    /**
     * Track purchase/transaction
     */
    public static function trackPurchase(float $value, string $currency = 'USD', string $item = ''): string
    {
        return self::track('purchase', [
            'value' => $value,
            'currency' => $currency,
            'item_name' => $item,
        ]);
    }

    /**
     * Track match event
     */
    public static function trackMatch(int $userId, int $matchedUserId): string
    {
        return self::track('match_created', [
            'user_id' => $userId,
            'matched_user_id' => $matchedUserId,
        ]);
    }

    /**
     * Track message sent
     */
    public static function trackMessage(string $type = 'text'): string
    {
        return self::track('message_sent', ['message_type' => $type]);
    }

    /**
     * Track profile view
     */
    public static function trackProfileView(int $profileId): string
    {
        return self::track('profile_view', ['profile_id' => $profileId]);
    }

    /**
     * Track search
     */
    public static function trackSearch(string $query = ''): string
    {
        return self::track('search', ['search_term' => $query]);
    }

    /**
     * Check if analytics is enabled
     */
    private static function isEnabled(): bool
    {
        $gaEnabled = filter_var(SiteSetting::get('google_analytics_enabled'), FILTER_VALIDATE_BOOLEAN);
        $firebaseEnabled = filter_var(SiteSetting::get('firebase_enabled'), FILTER_VALIDATE_BOOLEAN);
        
        return $gaEnabled || $firebaseEnabled;
    }

    /**
     * Generate tracking script
     */
    private static function getTrackingScript(string $event, string $paramsJson): string
    {
        $gaEnabled = filter_var(SiteSetting::get('google_analytics_enabled'), FILTER_VALIDATE_BOOLEAN);
        $firebaseEnabled = filter_var(SiteSetting::get('firebase_enabled'), FILTER_VALIDATE_BOOLEAN);

        if (!$gaEnabled && !$firebaseEnabled) {
            return '';
        }

        $script = '<script>';
        
        // Google Analytics tracking
        if ($gaEnabled) {
            $script .= "if(typeof gtag==='function'){gtag('event','{$event}',{$paramsJson});}";
        }
        
        // Firebase Analytics tracking
        if ($firebaseEnabled) {
            $script .= "if(window.logAnalyticsEvent&&window.firebaseAnalytics){window.logAnalyticsEvent(window.firebaseAnalytics,'{$event}',{$paramsJson});}";
        }
        
        $script .= '</script>';
        
        return $script;
    }

    /**
     * Get inline tracking script for blade templates
     */
    public static function inline(string $event, array $params = []): string
    {
        return self::track($event, $params);
    }
}
