<?php
namespace Absolute\ChangeTrackerPhpSdk;

use Closure;

/**
 * makeChangeTrackerService - changeTrackerService maker function
 * @param array $params
 * @return Closure
 */
function makeChangeTrackerService(array $params) : Closure {

    return function (string $hostName, string $apiSecretGet = '', string $apiSecretPost = '', int $tokenMinuteDuration = 5) use ( $params ){
        return new changeTrackerService($params, $hostName, $apiSecretGet, $apiSecretPost, $tokenMinuteDuration);
    };
}
