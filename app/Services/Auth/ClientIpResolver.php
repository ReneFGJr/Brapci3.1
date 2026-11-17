<?php

namespace App\Services\Auth;

class ClientIpResolver
{
    public static function resolve(string $forwardedFor, string $realIp, string $fallback): string
    {
        foreach (explode(',', $forwardedFor) as $candidate) {
            $ip = trim($candidate);
            if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                return $ip;
            }
        }

        $realIp = trim($realIp);
        if (filter_var($realIp, FILTER_VALIDATE_IP) !== false) {
            return $realIp;
        }

        return filter_var($fallback, FILTER_VALIDATE_IP) !== false ? $fallback : '';
    }
}
