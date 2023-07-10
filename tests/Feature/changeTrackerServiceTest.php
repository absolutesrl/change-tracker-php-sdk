<?php

use function Absolute\ChangeTrackerPhpSdk\makeChangeTrackerService;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;

test('use changeTrackerService.store to perform diff and store data', function (){
    $generateToken = fn() => 'TESTTOKEN';
    $store = fn() => json_decode('{"ok" : true}');
    $diff = fn() => (new Row('diffModel'));
    $createTable = fn() => (new Table());

    $changeTrackerService = makeChangeTrackerService(['generateToken' => $generateToken, 'store' => $store, 'diff'=> $diff, 'createTable'=> $createTable]);
    $service = $changeTrackerService('test', 'POSTSECRET', 10);

    $result = $service->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    expect($result)->toMatchObject(['ok' => true]);
});

test('changeTrackerService.store returns error in case of diff returns null', function () {
    $generateToken = fn() => 'TESTTOKEN';
    $store = fn() => json_decode('{"ok" : true}');
    $diff = fn() => null;
    $createTable = fn() => (new Table());

    $changeTrackerService = makeChangeTrackerService(['generateToken' => $generateToken, 'store' => $store, 'diff'=> $diff, 'createTable'=> $createTable]);
    $service = $changeTrackerService('test', 'POSTSECRET');

    $result = $service->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    expect($result)->toMatchObject(['ok'=> false]);
    var_dump($result->errorText);
});

test('changeTrackerService.store propagates remoteClient error', function() {
    $generateToken = fn() => 'TESTTOKEN';
    $remoteClientResponse = json_decode('{"ok": false, "errorText": "TEST ERROR TEXT"}');
    $store = fn() => $remoteClientResponse;
    $diff = fn() => new Row('diffModel');
    $createTable = fn() => (new Table());

    $changeTrackerService = makeChangeTrackerService(['generateToken' => $generateToken, 'store' => $store, 'diff'=> $diff, 'createTable'=> $createTable]);
    $service = $changeTrackerService('test', 'GETSECRET','POSTSECRET');

    $result = $service->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    expect($result)->toMatchObject(["ok" => false, "errorText" => "TEST ERROR TEXT"]);
});

test('check changeTrackerService.getToken arguments', function() {
    $generateToken = fn(...$args) => array_reduce($args, fn($p, $c) => $p.'-'.$c);

    $defaultMockLambda = fn() => null;

    $changeTrackerService = makeChangeTrackerService(['generateToken' => $generateToken, 'store' => $defaultMockLambda, 'diff'=> $defaultMockLambda, 'createTable'=> $defaultMockLambda]);

    $service = $changeTrackerService('test', 'GETSECRET', 'POSTSECRET');

    $result = $service->getToken('TABLENAME', 'ROWKEY');

    var_dump($result);
    $resultString ='-GETSECRET-TABLENAME-ROWKEY-5';

    expect($result)->toBe($resultString);
});
