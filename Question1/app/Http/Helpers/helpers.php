<?php

if (! function_exists('get_jwt_payload')) {
    function get_jwt_payload($jwt, $key = null, $default = null)
    {
        $jwt_array = explode('.', $jwt);
        if (empty($jwt_array[1])) {
            return $default;
        }

        $ret = json_decode(base64_decode($jwt_array[1]), true);
        if($key) {
            return $ret[$key] ?? $default;
        }
        return $ret ?? $default;
    }
}

if (! function_exists('get_jwt')) {
    function get_jwt()
    {
        return Request::header('authsports');
    }
}

if (! function_exists('get_rand_source')) {
    function get_rand_source($min = 1000000, $max = 9999999)
    {
        return mt_rand($min, $max);
    }
}

if (! function_exists('can_be_modified')) {
    function can_be_modified($check)
    {
        $source = config('gameserver.source');
        return $source == (int) $check;
    }
}

if (! function_exists('valid_admin_jwt')) {
    function valid_admin_jwt($jwt, $admin = true)
    {
        if (blank($jwt)) {
            return false;
        }

        $payload = get_jwt_payload($jwt);
        $id = data_get($payload, 'adminAccountId');
        if (blank($id)) {
            return false;
        }

        $acc = ($admin)
            ? new App\Library\GameServer\Admin\Admin()
            : new App\Library\GameServer\Partner\Partner();
        $account_detail = $acc->detail($jwt, $id);
        if (data_get($account_detail, 'data._key') !== $id) {
            return false;
        }
        return true;
    }
}

if (! function_exists('encodePlayerIdToPlayerHashId')) {
    function encodePlayerIdToPlayerHashId($player_id)
    {
        // Set the offset basis
        $hash = 0x811c9dc5;
        // If we have characters to process
        if (strlen($player_id) > 0) {
            // Make a character array from the string
            $string_array = str_split($player_id);
            // For each character
            foreach ($string_array as $chr) {
                // Xor with the current character
                $hash ^= ord($chr);
                // Multiply by prime
                $hash *= 0x01000193;
                // Clamp
                $hash &= 0xffffffff;
            }
        }
        return $hash;
    }
}

if (! function_exists('encodePlayerHashIdToModId')) {
    function encodePlayerHashIdToModId(int $player_id, int $num_of_sharding_db)
    {
        return $player_id % $num_of_sharding_db;
    }
}

if (! function_exists('encodePlayerIdToModId')) {
    function encodePlayerIdToModId(int $player_id, int $num_of_sharding_db) {
        return encodePlayerHashIdToModId(encodePlayerIdToPlayerHashId($player_id), $num_of_sharding_db);
    }
}

if (! function_exists('json_response')) {

    function json_response(): \App\Http\Responses\Response
    {
        return app('App\\Http\\Responses\\Response');
    }
}

if (! function_exists('device')) {
    function device()
    {
        return new \App\Library\Device();
    }
}

if (! function_exists('ipv4')) {

    /**
     * 取得 IPv4
     *
     * @return string|null
     */
    function ipv4()
    {
        $ip = request()->ip();

        return is_valid_ipv4($ip) ? $ip : null;
    }
}

if (! function_exists('ipv6')) {

    /**
     * 取得 IPv6
     *
     * @return string|null
     */
    function ipv6()
    {
        $ip = request()->ip();

        return is_valid_ipv6($ip) ? $ip : null;
    }
}

if (! function_exists('is_valid_ipv4')) {
    /**
     * 判斷是否為有效的 IPv4
     *
     * @param $ip
     * @param  bool  $private
     * @return bool
     */
    function is_valid_ipv4($ip, bool $private = false): bool
    {
        if ($private) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)
                && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}

if (! function_exists('is_valid_ipv6')) {
    /**
     * 判斷是否為有效的 IPv6
     *
     * @param $ip
     * @param  bool  $private
     * @return bool
     */
    function is_valid_ipv6($ip, bool $private = false): bool
    {
        if ($private) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)
                && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }
}

if (! function_exists('format_short_exception')) {
    /**
     * @param  Throwable  $e
     * @return string
     */
    function format_short_exception(string $class, \Throwable $e): string
    {
        return "[{$class}]".PHP_EOL.
            "Message: {$e->getMessage()}".PHP_EOL.
            "File: {$e->getFile()}".PHP_EOL.
            "Line: {$e->getLine()}";
    }
}

if (! function_exists('slack_notify')) {
    /**
     * slack 通知
     *
     * @return \Psr\Log\LoggerInterface
     */
    function slack_notify(): \Psr\Log\LoggerInterface
    {
        return \Log::channel('slack');
    }
}


if (! function_exists('parse_bool')) {
    /**
     * @param $value
     * @return bool
     */
    function parse_bool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
