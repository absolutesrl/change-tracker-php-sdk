<?php
namespace Absolute\ChangeTrackerPhpSdk\Core;
use Absolute\ChangeTrackerPhpSdk\Model\Table;

/**
 * store - perform POST request to host
 * @param string $hostName
 * @param string $token
 * @param Table|null $table
 * @return object|null
 */
function store(string $hostName, string $token, Table $table = null) : object | null {
    if ($table === null) return null;

    $baseUrl = "https://{$hostName}.hosts.changetracker.it";
    $tableName = urlencode($table->name);
    $encodedToken = urlencode($token);
    $url = "{$baseUrl}/?tableName={$tableName}&token={$encodedToken}";
    $body = json_encode($table);

    //Content-Type
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => 1,
        CURLOPT_HTTPHEADER=> [ 'accept' => 'application/json' ],
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_RETURNTRANSFER => true
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    curl_close($ch);

    if(curl_error($ch)) return json_decode('{"ok" => false, "errorText" => '.curl_error($ch).'}');

    return json_decode($response);
}
