<?php
//store integration test
require_once(__DIR__.'/../src/core/remoteClient.php');
require_once(__DIR__.'/../src/models/Table.php');
require_once(__DIR__.'/../src/models/Row.php');
require_once(__DIR__.'/../src/core/token.php');

function remoteClientStoreDataOnDynamoDB(){

    $hostName = 'hostname'; //hostname
    $secret = 'XXXXXXXXX'; //post token
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = generateToken($secret, $tableName, $rowKey);

    $res = store($hostName, $token, Table::createTable([new Row('TESTROWKEY')], 'TESTTABLE', 'TESTUSER', '127.0.0.1'));

    var_dump($res);
    //check data on test DB
};
remoteClientStoreDataOnDynamoDB();