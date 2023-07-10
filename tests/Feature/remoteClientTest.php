<?php

//store integration test
use function Absolute\ChangeTrackerPhpSdk\Core\generateToken;
use function Absolute\ChangeTrackerPhpSdk\Core\store;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;

test('integration test. Use remoteClient to store data on DynamoDB', function () {


    $hostName = 'hostname'; //hostname
    $secret = 'XXXXXXXXX'; //post token
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = generateToken($secret, $tableName, $rowKey);
    expect($token)->not->toBeNull();

    $res = store($hostName, $token, Table::createTable([new Row('TESTROWKEY')], 'TESTTABLE', 'TESTUSER', '127.0.0.1'));
    expect($res)->toMatchObject(['ok'=> true]);

    //check data on test DB
});
