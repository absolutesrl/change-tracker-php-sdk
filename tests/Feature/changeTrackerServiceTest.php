<?php

use Absolute\ChangeTrackerPhpSdk\ChangeTrackerService;
use Absolute\ChangeTrackerPhpSdk\core\ChangeCalculatorInterface;
use Absolute\ChangeTrackerPhpSdk\core\TokenInterface;
use Absolute\ChangeTrackerPhpSdk\core\RemoteClientInterface;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;
class MockToken implements TokenInterface {
    function generateToken(string $secret, string $tableName, string $rowKey, int $duration = 5)
    {
        return 'TESTTOKEN';
    }
}

class MockToken2 implements TokenInterface {
    function generateToken(string $secret, string $tableName, string $rowKey, int $duration = 5)
    {
        $args = [$secret, $tableName, $rowKey, $duration];
        return array_reduce($args, fn($p, $c) => $p.'-'.$c);
    }
}
class MockChangeCalculator implements ChangeCalculatorInterface {

    public function diff(Row $prev = null, Row $next = null)
    {
        return null;
    }
}
class MockChangeCalculator2 implements ChangeCalculatorInterface {
    public function diff(Row $prev = null, Row $next = null)
    {
        return new Row('diffModel');
    }
}

class MockRemoteClient implements RemoteClientInterface {

    public function store(string $hostName, string $token, Table $table = null)
    {
        return json_decode('{"ok" : true}');
    }
}
class MockRemoteClient2 implements RemoteClientInterface {

    public function store(string $hostName, string $token, Table $table = null)
    {
        return null;
    }
}
class MockRemoteClient3 implements RemoteClientInterface {
    public function store(string $hostName, string $token, Table $table = null)
    {
        return json_decode('{"ok": false, "errorText": "TEST ERROR TEXT"}');
    }
}

test('use changeTrackerService.store to perform diff and store data', function (){

    $changeTrackerService = new ChangeTrackerService( 'test', '', 'POSTSECRET', 10);
    $changeTrackerService->setToken(new MockToken());
    $changeTrackerService->setChangeCalculator(new MockChangeCalculator2());
    $changeTrackerService->setRemoteClient(new MockRemoteClient());
    $result = $changeTrackerService->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    expect($result)->toMatchObject(['ok' => true]);
});

test('changeTrackerService.store returns error in case of diff returns null', function () {

    $changeTrackerService = new ChangeTrackerService('test', '', 'POSTSECRET', 10);
    $changeTrackerService->setToken(new MockToken());
    $changeTrackerService->setChangeCalculator(new MockChangeCalculator());
    $changeTrackerService->setRemoteClient(new MockRemoteClient());

    $result = $changeTrackerService->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    expect($result)->toMatchObject(['ok'=> false]);
    var_dump($result->errorText);
});

test('changeTrackerService.store propagates remoteClient error', function() {

    $changeTrackerService = new ChangeTrackerService('test', 'GETSECRET', 'POSTSECRET', 10);
    $changeTrackerService->setToken(new MockToken());
    $changeTrackerService->setChangeCalculator(new MockChangeCalculator2());
    $changeTrackerService->setRemoteClient(new MockRemoteClient3());
    $result = $changeTrackerService->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    expect($result)->toMatchObject(["ok" => false, "errorText" => "TEST ERROR TEXT"]);
});

test('check changeTrackerService.getToken arguments', function() {

    $changeTrackerService = new ChangeTrackerService('test', 'GETSECRET', 'POSTSECRET', 10);
    $changeTrackerService->setToken(new mockToken2());
    $changeTrackerService->setChangeCalculator(new MockChangeCalculator());
    $changeTrackerService->setRemoteClient(new MockRemoteClient2());

    $result = $changeTrackerService->getToken('TABLENAME', 'ROWKEY');

    var_dump($result);
    $resultString ='-GETSECRET-TABLENAME-ROWKEY-5';

    expect($result)->toBeString($resultString);
});
