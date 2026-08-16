<?php

namespace App\Services\Ai;

use App\Models\Socials;

class ApiKeyAuthenticator
{
    public function findActiveUser(string $apiKey): ?array
    {
        $user = (new Socials())
            ->select('id_us, us_nome, us_email, us_apikey')
            ->where('us_apikey', $apiKey)
            ->where('us_apikey_active', 1)
            ->first();

        return is_array($user) ? $user : null;
    }
}
