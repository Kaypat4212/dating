<?php

namespace App\Services;

/**
 * Agora AccessToken2 builder — pure PHP, no package required.
 *
 * Implements the AccessToken2 spec:
 * https://docs.agora.io/en/video-calling/develop/authentication-workflow
 */
class AgoraTokenService
{
    // Privilege keys
    private const PRIV_JOIN_CHANNEL     = 1;
    private const PRIV_PUBLISH_AUDIO    = 2;
    private const PRIV_PUBLISH_VIDEO    = 3;
    private const PRIV_PUBLISH_DATA     = 4;

    private string $appId;
    private string $appCertificate;

    public function __construct()
    {
        $this->appId          = config('services.agora.app_id', '');
        $this->appCertificate = config('services.agora.app_certificate', '');
    }

    /**
     * Generate an RTC token for a user to join a channel.
     *
     * @param  string  $channelName  Agora channel name
     * @param  int     $uid          Agora user ID (use 0 to auto-assign)
     * @param  int     $expireSeconds  Token lifetime in seconds (default 1 hour)
     * @return string  Base64-encoded token
     */
    public function generateRtcToken(string $channelName, int $uid = 0, int $expireSeconds = 3600): string
    {
        if (empty($this->appId) || empty($this->appCertificate)) {
            throw new \RuntimeException('Agora App ID and App Certificate must be set in config/services.php');
        }

        $now    = time();
        $expire = $now + $expireSeconds;

        // Salt: random 32-bit integer
        $salt = random_int(1, 0x7FFFFFFF);

        // Privileges map: [privilege_id => expire_timestamp]
        $privileges = [
            self::PRIV_JOIN_CHANNEL  => $expire,
            self::PRIV_PUBLISH_AUDIO => $expire,
            self::PRIV_PUBLISH_VIDEO => $expire,
            self::PRIV_PUBLISH_DATA  => $expire,
        ];

        // Pack privileges
        $packedPrivileges = '';
        foreach ($privileges as $key => $ts) {
            $packedPrivileges .= pack('vV', $key, $ts);
        }
        $privCount = count($privileges);

        // Pack service RTC payload
        // service_type=1 (RTC), channel_name, uid, privileges
        $channelNameBytes = pack('v', strlen($channelName)) . $channelName;
        $uidStr           = (string) $uid;
        $uidBytes         = pack('v', strlen($uidStr)) . $uidStr;
        $privilegesPacked = pack('v', $privCount) . $packedPrivileges;

        $serviceType = pack('v', 1); // SERVICE_TYPE_RTC = 1
        $servicePayload = $serviceType . $channelNameBytes . $uidBytes . $privilegesPacked;

        // Pack the full message: [version=1][app_id][issues_at][expire][salt][service_count=1][service_payload]
        $appIdBytes = pack('v', strlen($this->appId)) . $this->appId;
        $header     = pack('VVV', $now, $expire, $salt);

        // services section: count + type_tag + payload
        $payloadLen   = pack('v', strlen($servicePayload));
        $servicesFull = pack('v', 1) . $serviceType . $payloadLen . substr($servicePayload, 2); // skip re-packing type

        // Build the signing body: appId + issues_at + expire + salt + services
        $msg = $appIdBytes . pack('V', $now) . pack('V', $expire) . pack('V', $salt)
             . pack('v', 1) . $servicePayload;

        // HMAC-SHA256 signature
        $signature = hash_hmac('sha256', $msg, $this->appCertificate, true);

        // Final token body: signature + msg
        $tokenBody = pack('v', strlen($signature)) . $signature . $msg;

        return '007' . base64_encode($tokenBody);
    }

    /**
     * Return whether Agora is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->appId) && !empty($this->appCertificate)
            && $this->appId !== 'your_agora_app_id';
    }
}
