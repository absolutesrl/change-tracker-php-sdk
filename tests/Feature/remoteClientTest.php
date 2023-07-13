<?php

//store integration test
use Absolute\ChangeTrackerPhpSdk\Core\Token;
use Absolute\ChangeTrackerPhpSdk\Core\RemoteClient;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;

test('integration test. Use remoteClient to store data on DynamoDB', function () {
    $tokenService = new Token();
    $remoteService = new RemoteClient();

    $hostName = 'hostname'; //hostname
    $secret = 'XXXXXXXXX'; //post token
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = $tokenService->generateToken($secret, $tableName, $rowKey);
    expect($token)->not->toBeNull();

    $res = $remoteService->store($hostName, $token, Table::createTable([new Row('TESTROWKEY')], 'TESTTABLE', 'TESTUSER', '127.0.0.1'));
    expect($res)->toMatchObject(['ok'=> true]);

    //check data on test DB
});
