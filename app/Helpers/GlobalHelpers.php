<?php

namespace App\Helpers;

class GlobalHelpers
{
    /**
     * Detect if the request is from a mobile device
     */
    public static function isMobileDevice($userAgent, $x_mobile_device): bool
    {

        // Check for mobile user agents
        $mobileAgents = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'Windows Phone', 'Opera Mini', 'IEMobile',
        ];

        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }

        // Also check for mobile-specific headers
        if ($x_mobile_device === 'true') {
            return true;
        }

        return false;
    }
}
