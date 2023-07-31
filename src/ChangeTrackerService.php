<?php
namespace Absolute\ChangeTrackerPhpSdk;

use Absolute\ChangeTrackerPhpSdk\Core\ChangeCalculatorInterface;
use Absolute\ChangeTrackerPhpSdk\Core\RemoteClientInterface;
use Absolute\ChangeTrackerPhpSdk\Core\TokenInterface;
use Absolute\ChangeTrackerPhpSdk\Core\RemoteClient;
use Absolute\ChangeTrackerPhpSdk\Core\ChangeCalculator;
use Absolute\ChangeTrackerPhpSdk\Core\Token;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;

/**
 * change tracker interface
 * @type {class}
 * @param {string} $hostName - the project host name
 * @param {string} $apiSecretGet - API get secret
 * @param {string} $apiSecretPost- API post secret
 * @param {int} [$tokenMinuteDuration=5] - the token duration in minutes
 **/
class ChangeTrackerService {
    public TokenInterface $token;
    public ChangeCalculatorInterface $changeCalculator;
    public RemoteClientInterface $remoteClient;
    public string $hostName;
    public string $apiSecretGet;
    public string $apiSecretPost;
    public int $tokenMinuteDuration;

    /**
     * @constructor
     * @param string $hostName
     * @param string $apiSecretGet
     * @param string $apiSecretPost
     * @param int $tokenMinuteDuration
     */
    function __construct(string $hostName, string $apiSecretGet, string $apiSecretPost, int $tokenMinuteDuration = 5 ){
        $this->token = new Token();
        $this->changeCalculator = new ChangeCalculator();
        $this->remoteClient = new RemoteClient();
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
    public function store(string $tableName, string $userName, string $rowDescription, Row $prevModel, Row $nextModel, string $ipAddress = '') {
        $token = $this->token->generateToken($this->apiSecretPost, $tableName, '', $this->tokenMinuteDuration);
        $row = $this->changeCalculator->diff($prevModel, $nextModel);

        if (!$row) return json_decode('{"ok" : false, "errorText": "ChangeTracker, changeCalculator: missing or invalid changeCalculator models"}');

        $row->desc = $rowDescription;

        $table = Table::createTable([$row], $tableName, $userName, $ipAddress);

        if (!$table) return json_decode('{"ok" : false, "errorText": "ChangeTracker, createTable: invalid rows model"}');

        return $this->remoteClient->store($this->hostName, $token, $table);
    }

    /**
     * @param string $tableName
     * @param string $rowKey
     * @param int $thisTokenMinuteDuration
     * @return mixed
     */
    public function getToken(string $tableName, string $rowKey, int $thisTokenMinuteDuration = 5) : string {
        return $this->token->generateToken( $this->apiSecretGet, $tableName, $rowKey, $thisTokenMinuteDuration );
    }

    public function setToken(TokenInterface $token){
        $this->token = $token;
    }

    public function setRemoteClient(RemoteClientInterface $remoteClient){
        $this->remoteClient = $remoteClient;
    }

    public function setChangeCalculator(ChangeCalculatorInterface $changeCalculator){
        $this->changeCalculator = $changeCalculator;
    }
}
