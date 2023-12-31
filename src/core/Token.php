<?php
namespace Absolute\ChangeTrackerPhpSdk\Core;
/**
 * generateToken - create a jwtToken
 * @param string $secret
 * @param string $tableName
 * @param string $rowKey
 * @param int $duration
 * @return String
 */
class Token implements TokenInterface {

    function generateToken(string $secret, string $tableName, string $rowKey, int $duration = 5) : string {
        $payload =  [];

        $payload['table'] = $tableName;

        if ($rowKey) $payload['key'] = $rowKey;

        $iat = time();
        $exp = $iat + $duration * 60;

        $payload['iat'] = $iat;
        $payload['exp'] = $exp;

        $signing_key = $secret;
        $header = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];
        $header = $this->base64_url_encode(json_encode($header));

        $payload = $this->base64_url_encode(json_encode($payload));
        $signature = $this->base64_url_encode(hash_hmac('sha256', "$header.$payload", $signing_key, true));
        return "$header.$payload.$signature";
    }
    function base64_url_encode($text):String{
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }
}
