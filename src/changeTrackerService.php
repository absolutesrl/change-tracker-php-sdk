<?php
namespace Absolute\ChangeTrackerPhpSdk;

use Absolute\ChangeTrackerPhpSdk\Model\Row;

/**
 * change tracker interface
 * @type {class}
 * @param {string} $hostName - the project host name
 * @param {string} $apiSecretGet - API get secret
 * @param {string} $apiSecretPost- API post secret
 * @param {int} [$tokenMinuteDuration=5] - the token duration in minutes
 **/
class changeTrackerService extends ChangeTrackerServiceContainer {
    public string $hostName;
    public string $apiSecretGet;
    public string $apiSecretPost;
    public int $tokenMinuteDuration;

    /**
     * @constructor
     * @param array $params
     * @param string $hostName
     * @param string $apiSecretGet
     * @param string $apiSecretPost
     * @param int $tokenMinuteDuration
     */
    function __construct(array $params, string $hostName, string $apiSecretGet, string $apiSecretPost, int $tokenMinuteDuration = 5){
        parent::__construct($params);
        $this->hostName = $hostName;
        $this->apiSecretGet = $apiSecretGet;
        $this->apiSecretPost = $apiSecretPost;
        $this->tokenMinuteDuration = $tokenMinuteDuration;
    }

    /**
     * store - stores data on change tracker
     * @param string $tableName
     * @param string $userName
     * @param string $rowDescription
     * @param Row $prevModel
     * @param Row $nextModel
     * @param string $ipAddress
     * @return mixed
     */
    public function store(string $tableName, string $userName, string $rowDescription, Row $prevModel, Row $nextModel, string $ipAddress = '') : object | null {
        $token = parent::generateToken($this->apiSecretPost, $tableName, '', $this->tokenMinuteDuration);
        $row = parent::diff($tableName, $prevModel, $nextModel);

        if (!$row) return json_decode('{"ok" : false, "errorText": "ChangeTracker, diff: missing or invalid diff models"}');

        $row->desc = $rowDescription;

        $table = parent::createTable([$row], $tableName, $userName, $ipAddress);

        if (!$table) return json_decode('{"ok" : false, "errorText": "ChangeTracker, createTable: invalid rows model"}');

        return parent::makeStore($this->hostName, $token, $table);
    }

    /**
     * @constructor
     * @param string $tableName
     * @param string $rowKey
     * @param int $thisTokenMinuteDuration
     * @return mixed
     */
    public function getToken(string $tableName, string $rowKey, int $thisTokenMinuteDuration = 5) : string {
        return parent::generateToken( $this->apiSecretGet, $tableName, $rowKey, $thisTokenMinuteDuration );
    }
}
