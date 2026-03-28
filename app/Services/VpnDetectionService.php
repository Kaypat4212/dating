<?php

namespace App\Services;

use App\Models\VpnDetectionLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VpnDetectionService
{
    /**
     * Check if an IP address is using a VPN/Proxy
     *
     * @param string $ip
     * @return array ['is_vpn' => bool, 'confidence' => int, 'details' => array, 'provider' => string|null]
     */
    public function detect(string $ip): array
    {
        // Skip detection for local/private IPs
        if ($this->isPrivateIP($ip)) {
            return [
                'is_vpn' => false,
                'confidence' => 0,
                'details' => ['reason' => 'Private/Local IP'],
                'provider' => null,
            ];
        }

        // Check cache first (cache for 24 hours)
        $cacheKey = "vpn_check_{$ip}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $detectionMethods = [];
        $vpnScore = 0;
        $maxScore = 0;
        $detectedProvider = null;

        // Method 1: Check against known VPN IP ranges
        $knownVpnCheck = $this->checkKnownVpnRanges($ip);
        $detectionMethods['known_vpn_ranges'] = $knownVpnCheck;
        if ($knownVpnCheck['detected']) {
            $vpnScore += 100;
            $detectedProvider = $knownVpnCheck['provider'];
        }
        $maxScore += 100;

        // Method 2: Check common VPN DNS patterns
        $dnsCheck = $this->checkDnsPattern($ip);
        $detectionMethods['dns_pattern'] = $dnsCheck;
        if ($dnsCheck['detected']) {
            $vpnScore += 50;
            $detectedProvider = $detectedProvider ?? $dnsCheck['provider'];
        }
        $maxScore += 50;

        // Method 3: Use external API if configured (IPHub.info free tier)
        if ($apiKey = config('services.iphub.key')) {
            $apiCheck = $this->checkWithIPHub($ip, $apiKey);
            $detectionMethods['iphub_api'] = $apiCheck;
            if ($apiCheck['detected']) {
                $vpnScore += 80;
                $detectedProvider = $detectedProvider ?? 'IPHub Detection';
            }
            $maxScore += 80;
        }

        // Method 4: ProxyCheck.io API (free tier - 100 queries/day)
        if ($apiKey = config('services.proxycheck.key')) {
            $proxyCheck = $this->checkWithProxyCheck($ip, $apiKey);
            $detectionMethods['proxycheck_api'] = $proxyCheck;
            if ($proxyCheck['detected']) {
                $vpnScore += 80;
                $detectedProvider = $detectedProvider ?? $proxyCheck['provider'];
            }
            $maxScore += 80;
        }

        // Method 5: Check IP quality score (basic heuristics)
        $qualityCheck = $this->checkIpQuality($ip);
        $detectionMethods['ip_quality'] = $qualityCheck;
        if ($qualityCheck['suspicious']) {
            $vpnScore += 30;
        }
        $maxScore += 30;

        // Calculate confidence percentage
        $confidence = $maxScore > 0 ? (int) (($vpnScore / $maxScore) * 100) : 0;

        // Read threshold from SiteSetting (DB) with fallback to env or default 40
        $dbThreshold = \App\Models\SiteSetting::get('vpn_confidence_threshold', null);
        $threshold   = $dbThreshold !== null
            ? (int) $dbThreshold
            : (int) env('VPN_CONFIDENCE_THRESHOLD', 40);

        // Determine if VPN is detected
        $isVpn = $confidence >= $threshold;

        $result = [
            'is_vpn' => $isVpn,
            'confidence' => $confidence,
            'details' => $detectionMethods,
            'provider' => $detectedProvider,
        ];

        // Cache the result for 24 hours
        Cache::put($cacheKey, $result, now()->addHours(24));

        return $result;
    }

    /**
     * Check if IP is in private/local range
     */
    private function isPrivateIP(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }
        return false;
    }

    /**
     * Check against known VPN provider IP ranges
     */
    private function checkKnownVpnRanges(string $ip): array
    {
        $knownVpnProviders = [
            'NordVPN' => $this->nordVpnRanges(),
            'ExpressVPN' => $this->expressVpnRanges(),
            'ProtonVPN' => $this->protonVpnRanges(),
            'Surfshark' => $this->surfsharkRanges(),
            'CyberGhost' => $this->cyberghostRanges(),
            'TunnelBear' => $this->tunnelbearRanges(),
            'Windscribe' => $this->windscribeRanges(),
        ];

        $ipLong = ip2long($ip);
        if ($ipLong === false) {
            return ['detected' => false, 'provider' => null];
        }

        foreach ($knownVpnProviders as $provider => $ranges) {
            foreach ($ranges as $range) {
                if ($this->ipInRange($ipLong, $range)) {
                    return ['detected' => true, 'provider' => $provider];
                }
            }
        }

        return ['detected' => false, 'provider' => null];
    }

    /**
     * Check if IP is in CIDR range
     */
    private function ipInRange(int $ipLong, string $cidr): bool
    {
        if (strpos($cidr, '/') === false) {
            return $ipLong === ip2long($cidr);
        }

        [$range, $netmask] = explode('/', $cidr, 2);
        $rangeLong = ip2long($range);
        $wildcardLong = pow(2, (32 - $netmask)) - 1;
        $netmaskLong = ~$wildcardLong;

        return ($ipLong & $netmaskLong) === ($rangeLong & $netmaskLong);
    }

    /**
     * Check DNS reverse lookup patterns
     */
    private function checkDnsPattern(string $ip): array
    {
        try {
            $hostname = gethostbyaddr($ip);
            if ($hostname === $ip) {
                return ['detected' => false, 'provider' => null, 'hostname' => null];
            }

            $vpnPatterns = [
                'vpn', 'proxy', 'tunnel', 'relay', 'hide', 'anonym',
                'nordvpn', 'expressvpn', 'protonvpn', 'surfshark',
                'datacenter', 'hosting', 'cloud', 'server'
            ];

            $hostnameLower = strtolower($hostname);
            foreach ($vpnPatterns as $pattern) {
                if (strpos($hostnameLower, $pattern) !== false) {
                    return [
                        'detected' => true,
                        'provider' => 'DNS Pattern: ' . $hostname,
                        'hostname' => $hostname
                    ];
                }
            }

            return ['detected' => false, 'provider' => null, 'hostname' => $hostname];
        } catch (\Exception $e) {
            return ['detected' => false, 'provider' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check with IPHub API
     */
    private function checkWithIPHub(string $ip, string $apiKey): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['X-Key' => $apiKey])
                ->get("https://v2.api.iphub.info/ip/{$ip}");

            if ($response->successful()) {
                $data = $response->json();
                // IPHub block values: 0 = residential, 1 = proxy/vpn, 2 = datacenter
                $block = $data['block'] ?? 0;
                
                return [
                    'detected' => $block >= 1,
                    'provider' => $block >= 1 ? 'VPN/Proxy (IPHub)' : null,
                    'block_type' => $block,
                    'country' => $data['countryCode'] ?? null,
                ];
            }

            return ['detected' => false, 'error' => 'API request failed'];
        } catch (\Exception $e) {
            Log::warning('IPHub API error: ' . $e->getMessage());
            return ['detected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check with ProxyCheck.io API
     */
    private function checkWithProxyCheck(string $ip, string $apiKey): array
    {
        try {
            $response = Http::timeout(5)
                ->get("https://proxycheck.io/v2/{$ip}", [
                    'key' => $apiKey,
                    'vpn' => 1,
                    'asn' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $ipData = $data[$ip] ?? [];
                
                $isProxy = ($ipData['proxy'] ?? 'no') === 'yes';
                $type = $ipData['type'] ?? null;
                
                return [
                    'detected' => $isProxy,
                    'provider' => $isProxy ? ($type ? ucfirst($type) : 'VPN/Proxy') : null,
                    'type' => $type,
                    'country' => $ipData['isocode'] ?? null,
                ];
            }

            return ['detected' => false, 'error' => 'API request failed'];
        } catch (\Exception $e) {
            Log::warning('ProxyCheck API error: ' . $e->getMessage());
            return ['detected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Basic IP quality heuristics
     */
    private function checkIpQuality(string $ip): array
    {
        $suspicious = false;
        $reasons = [];

        // Check if IP has multiple failed attempts in logs
        $recentAttempts = VpnDetectionLog::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($recentAttempts >= 5) {
            $suspicious = true;
            $reasons[] = 'Multiple detection attempts from same IP';
        }

        return [
            'suspicious' => $suspicious,
            'reasons' => $reasons,
            'recent_attempts' => $recentAttempts,
        ];
    }

    /**
     * Log VPN detection attempt
     */
    public function logDetection(string $ip, ?int $userId, array $result, string $action = 'blocked'): void
    {
        try {
            VpnDetectionLog::create([
                'ip_address' => $ip,
                'user_id' => $userId,
                'is_vpn' => $result['is_vpn'],
                'confidence' => $result['confidence'],
                'provider' => $result['provider'],
                'detection_details' => json_encode($result['details']),
                'action_taken' => $action,
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log VPN detection: ' . $e->getMessage());
        }
    }

    /**
     * Known VPN IP ranges (sample data - should be updated regularly)
     * In production, consider using a database or external service for this
     */
    private function nordVpnRanges(): array
    {
        return [
            '185.220.100.0/24',
            '185.220.101.0/24',
            '185.220.102.0/24',
            '185.246.208.0/24',
            '194.5.220.0/24',
        ];
    }

    private function expressVpnRanges(): array
    {
        return [
            '213.163.64.0/24',
            '213.163.65.0/24',
            '213.163.66.0/24',
        ];
    }

    private function protonVpnRanges(): array
    {
        return [
            '185.159.156.0/24',
            '185.159.157.0/24',
            '185.159.158.0/24',
        ];
    }

    private function surfsharkRanges(): array
    {
        return [
            '85.239.232.0/24',
            '85.239.233.0/24',
        ];
    }

    private function cyberghostRanges(): array
    {
        return [
            '89.238.172.0/24',
            '89.238.173.0/24',
        ];
    }

    private function tunnelbearRanges(): array
    {
        return [
            '104.153.88.0/24',
        ];
    }

    private function windscribeRanges(): array
    {
        return [
            '104.234.204.0/24',
        ];
    }
}
